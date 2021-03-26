<?php

namespace Kusikusi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Config;
use Kusikusi\Models\EntityRelation;
use Kusikusi\Models\Entity;

class MediumBase extends Entity
{
    protected $contentFields = [ "title", "description", "transcript", "caption" ];
    protected $propertiesFields = [ "size", "lang", "format", "length", "exif", "width", "height" ];

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
            'isWebImage' => array_search(strtolower($format), Config::get('media.formats.webImages', ['jpeg', 'jpg', 'png', 'gif', 'svg'])) !== false,
            'isImage' => array_search(strtolower($format), Config::get('media.formats.images', ['jpeg', 'jpg', 'png', 'gif', 'tif', 'tiff', 'iff', 'bmp', 'psd', 'svg'])) !== false,
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
    public static function clearStatic($entity_id = null) {
        $cleared = [];
        if ($entity_id === '*') {
            $directories = Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->directories(null, false);
            foreach ($directories as $directory) {
                $deleted = Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->deleteDirectory($directory, true);
                if ($deleted) $cleared[] = $directory;
            }
        } else if ($entity_id) {
            if (Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->deleteDirectory($entity_id, true)) $cleared[] = $entity_id;
        }
        return $cleared;
    }
}