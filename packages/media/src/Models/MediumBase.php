<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use Kusikusi\Models\EntityRelation;
use Kusikusi\Models\Entity;

class MediumBase extends Entity
{
    const MODEL_NAME = "Medium";

    protected function getTitleAsSlug($preset) {
        $filename = isset($this['content']['title']) ? Str::slug($this['content']['title']) : 'file';
        $fileformat = Arr::get(Config::get('kusikusi_media.presets', []), "{$preset}.format", Str::slug($this->properties['format'] ?? 'bin'));
        return "{$filename}.{$fileformat}";
    }
    protected function getUrl($preset) {
        if (isset($this->properties['format'])) {
            if ($this->properties['format'] === 'svg') {
                $preset = 'original';
            }
        }
        if ((isset($this->properties['isWebImage']) && $this->properties['isWebImage']) || $preset == 'original') {
            return Config::get('filesystems.disks.'.Config::get('kusikusi_media.static_storage.drive').'.url')."/".Config::get('kusikusi_media.prefix', 'media')."/$this->id/$preset/{$this->getTitleAsSlug($preset)}";
        }
        return null;
    }
    protected static function getProperties($file) {
        $typeOfFile = gettype($file) === 'object' ? Str::afterLast(get_class($file), '\\') : (gettype($file) === 'string' ? 'path' : 'unknown');
        if ($typeOfFile === 'UploadedFile') {
            $format = strtolower($file->getClientOriginalExtension() ? $file->getClientOriginalExtension() : $file->guessClientExtension());
            $mimeType = $file->getClientMimeType();
            $originalName = $file->getClientOriginalName();
            $size = $file->getSize();
        } else if ($typeOfFile === 'path') {
            $format = strtolower(Str::afterLast($file, '.'));
            $mimes = new MimeTypes;
            $mimeType =  $mimes->getMimeType($format);
            $originalName = Str::afterLast($file, '/');
            $size = null;
        } else {
            $format = 'bin';
            $mimeType = 'application/octet-stream';
            $originalName = 'file.bin';
            $size = null;
        }
        $format = $format == 'jpeg' ? 'jpg': $format;
        $properties = [
            'format' => $format,
            'mimeType' => $mimeType,
            'originalName' => $originalName,
            'size' => $size,
            'isWebImage' => array_search(strtolower($format), Config::get('media.formats.webImages', ['jpeg', 'jpg', 'png', 'gif', 'svg', 'webp'])) !== false,
            'isImage' => array_search(strtolower($format), Config::get('media.formats.images', ['jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff', 'iff', 'bmp', 'psd', 'svg', 'webp'])) !== false,
            'isAudio' => array_search(strtolower($format), Config::get('media.formats.audios', ['mp3', 'wav', 'aiff', 'aac', 'oga', 'pcm', 'flac'])) !== false,
            'isWebAudio' => array_search(strtolower($format), Config::get('media.formats.webAudios', ['mp3', 'oga'])) !== false,
            'isVideo' => array_search(strtolower($format), Config::get('media.formats.videos', ['mov', 'mp4', 'qt', 'avi', 'mpe', 'mpeg', 'ogg', 'm4p', 'm4v', 'flv', 'wmv'])) !== false,
            'isWebVideo' => array_search(strtolower($format), Config::get('media.formats.webVideos', ['webm', 'mp4', 'ogg', 'm4p', 'm4v'])) !== false,
            'isDocument' => array_search(strtolower($format), Config::get('media.formats.documents', ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf', 'htm', 'html', 'txt', 'rtf', 'csv', 'pps', 'ppsx', 'odf', 'key', 'pages', 'numbers'])) !== false
        ];
        $properties['type'] = $properties['isImage'] ? 'image' : ($properties['isAudio'] ? 'audio' : ($properties['isVideo'] ? 'video' : ($properties['isDocument'] ? 'document' : 'file')));
        if ($properties['isImage'] && $format !== 'svg') {
            if ($typeOfFile === 'UploadedFile') {
                $image = Image::make($file->getRealPath());
            } else if ($typeOfFile === 'path') {
                $image = Image::make($file);
            }
            if ($image) {
                $properties['width'] = $image->width();
                $properties['height'] = $image->height();
                $properties['exif'] = $image->exif();
            }
        } else {
            $properties['exif'] = null;
            $properties['width'] = null;
            $properties['height'] = null;
        }
        return $properties;
    }
    public function processPreset($preset, $friendly) {
        $originalFilePath =  Config::get('kusikusi_media.original_storage.folder') .'/'. $this->id . '/file.' . (isset($this->properties['format']) ? $this->properties['format'] : 'bin');
        $publicFilePath = Config::get('kusikusi_media.original_storage.folder') .'/'. $this->id .'/'. $preset .'/'. $friendly;
        $presetSettings = Config::get('kusikusi_media.presets.'.$preset, null);
        if (NULL === $presetSettings && $preset !== 'original') {
            abort(404, "No media preset '$preset' found");
        }

        if (!self::originalDisk()->exists($originalFilePath)) {
            abort(404, 'File for medium ' . $originalFilePath . ' not found');
        }

        if (array_search($this->properties['format'], ['jpg', 'png', 'gif', 'webp']) === FALSE || $preset === 'original') {
            $headers = [];
            if ($this->properties['format'] === 'svg') {
                $headers = ['Content-Type' => 'image/svg+xml'];
            }
            if (Config::get('kusikusi_media.copy_original_to_static', false)) {
                self::staticDisk()
                    ->put($publicFilePath, self::originalDisk()
                    ->get($originalFilePath));
            }
            return self::originalDisk()
                ->response($originalFilePath, null, $headers);
        }

        // Set default values if not set
        data_fill($presetSettings, 'width', 256);  // int
        data_fill($presetSettings, 'height', 256); // int
        data_fill($presetSettings, 'scale', 'cover'); // contain | cover | fill
        data_fill($presetSettings, 'alignment', 'center'); // only if scale is 'cover' or 'contain' with background: top-left | top | top-right | left | center | right | bottom-left | bottom | bottom-right
        data_fill($presetSettings, 'background', null); // only if scale is 'contain' #HEXCODE or the image has transparency
        data_fill($presetSettings, 'crop', true); // only if scale is 'contain': true | false
        data_fill($presetSettings, 'quality', 80); // 0 - 100 for jpg | 1 - 8, (bits) for gif | 1 - 8, 24 (bits) for png
        data_fill($presetSettings, 'format', 'jpg'); // jpg | gif | png
        data_fill($presetSettings, 'effects', []); // ['colorize' => [50, 0, 0], 'greyscale' => [] ]


        // The fun
        $filedata = self::originalDisk()->get($originalFilePath);
        $image = Image::make($filedata);
        $background = array(255, 255, 255, 255);
        if ($presetSettings['background'] !== null) {
            $presetSettings['background'] = str_replace('#', '', $presetSettings['background']);
            switch (strlen($presetSettings['background'])) {
                case 3:
                case 6:
                    $background = $presetSettings['background'];
                    break;
                case 8:
                    $background = str_split($presetSettings['background'], 2);
                    foreach($background as &$channel) {
                      $channel = hexdec($channel);
                    }
                    $background[3] = $background[3] / 255;
                    break;
            }
            $canvas = Image::canvas($image->width(), $image->height(), $background);
            $image = $canvas->insert($image);
        }
        if ($presetSettings['scale'] === 'cover') {
            $image->fit($presetSettings['width'], $presetSettings['height'], NULL, $presetSettings['alignment']);
        } elseif ($presetSettings['scale'] === 'fill') {
            $image->resize($presetSettings['width'], $presetSettings['height']);
        } elseif ($presetSettings['scale'] === 'contain') {
            $image->resize($presetSettings['width'], $presetSettings['height'], function ($constraint) {
                $constraint->aspectRatio();
            });
            if ($presetSettings['crop'] === false) {
                $image->resizeCanvas($presetSettings['width'], $presetSettings['height'], $presetSettings['alignment'], false, $background);
            }
        }

        foreach ($presetSettings['effects'] as $key => $value) {
            $image->$key(...$value);
        }

        $image->encode($presetSettings['format'], $presetSettings['quality']);
        self::staticDisk()->put($publicFilePath, $image);
    }
    public function processUpload($role, UploadedFile $file)
    {
        $fileProperties = self::processUploadById($this->id, $role, $file);
        if ($role === 'file') {
            // TODO: For some reason the exit properties freezes the process once is returned as json
           /* if (isset($fileProperties['exif'])) {
               foreach ($fileProperties['exif'] as $prop => $value) {
                   if (Str::startsWith($prop, "UndefinedTag")) {
                       unset($fileProperties['exif'][$prop]);
                   }
               }
           } */
           unset($fileProperties['exif']);
           $this['properties'] = (array) $this['properties'] ?? [];
           if (empty($this['properties']) || (is_array($this['properties'] && count($this['properties']) === 0))) {
               $this['properties'] = $fileProperties;
           } else {
               $this['properties'] = array_merge((array) $this['properties'], $fileProperties);
           }
           $this->save();
       }
       return $fileProperties;
    }
    public static function processUploadById($entity_id, $role, UploadedFile $file) {
        $properties = self::getProperties($file);
        $storageFileName = $role . '.' . $properties['format'];
        self::deleteOriginal($entity_id);
        self::clearStatic($entity_id);
        self::originalDisk()->putFileAs(Config::get('kusikusi_media.original_storage.folder').'/'.$entity_id, $file, $storageFileName);
        return $properties;
    }
    public static function deleteOriginal($entity_id) {
        if (!$entity_id) abort(400);
        $files = self::originalDisk()->files(Config::get('kusikusi_media.original_storage.folder').'/'.$entity_id);
        return self::originalDisk()->delete($files, true);
    }
    public static function clearStatic($entity_id = null, $preset = null) {
        if (!$entity_id) abort(400);
        $cleared = [];
        if ($entity_id === '*') {
            $entitiesDirectories = self::originalDisk()->directories(Config::get('kusikusi_media.static_storage.folder'));
        } else {
            $entitiesDirectories = [Config::get('kusikusi_media.static_storage.folder').'/'.$entity_id];
        }
        foreach ($entitiesDirectories as $entityDirectory) {
            if (!$preset) {
                $presetDirectories = self::originalDisk()->directories($entityDirectory);
            } else {
                $presetDirectories = [$entityDirectory.'/'.$preset];
            }
            foreach ($presetDirectories as $presetDirectory) {
                $cleared[$presetDirectory] = self::staticDisk()->deleteDirectory($presetDirectory, true);
            }
        }
        return $cleared;
    }
    private static function originalDisk() {
        return Storage::disk(Config::get('kusikusi_media.original_storage.drive'));
    }
    private static function staticDisk() {
        return Storage::disk(Config::get('kusikusi_media.static_storage.drive'));
    }
    protected static function boot()
    {
        static::deleted(function (Model $medium) {
            if ($medium->isForceDeleting()) {
                $medium->clearStatic($medium->id);
                $medium->deleteOriginal($medium->id);
            }
        });
        parent::boot();
    }
}
