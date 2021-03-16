<?php

namespace Kusikusi\Http\Controllers;

use Kusikusi\Models\Entity;
use Kusikusi\Models\MediumBase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class MediumController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Gets a medium: Optimized using a preset if it is an image or the original one if not.
     *
     * @group Media
     * @urlParam entity_id required The id of the entity of type medium to get. Example: djr4sd7Gmd
     * @urlParam preset required A preset configured in config/media.php to process the image. Example: icon.
     * @return Response
     */
    public function get(Request $request, $entity_id, $preset, $friendly = NULL)
    {
        // TODO: Review if the user can read the media
        $entity = Entity::isPublished()->findOrFail($entity_id);
        // Paths
        $originalFilePath =  Config::get('kusikusi_media.original_storage.folder') .'/'. $entity_id . '/file.' . (isset($entity->properties['format']) ? $entity->properties['format'] : 'bin');
        $presetSettings = Config::get('kusikusi_media.presets', null);
        $publicFilePath = Config::get('kusikusi_media.original_storage.folder') .'/'. $entity_id .'/'. $preset .'/'. $friendly;
        if ($exists = Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->exists($publicFilePath)) {
            return $this->getStaticMedium($publicFilePath);
        }

        if (NULL === $presetSettings && $preset !== 'original') {
            abort(404, "No media preset '$preset' found");
        }

        if (!$exists = Storage::disk(Config::get('kusikusi_media.original_storage.drive'))->exists($originalFilePath)) {
            abort(404, 'File for medium ' . $originalFilePath . ' not found');
        }

        if (array_search($entity->properties['format'], ['jpg', 'png', 'gif']) === FALSE || $preset === 'original') {
            $headers = [];
            if ($entity->properties['format'] === 'svg') {
                $headers = ['Content-Type' => 'image/svg+xml'];
            }
            if (Config::get('kusikusi_media.copy_original_to_static', false)) {
                Storage::disk(Config::get('kusikusi_media.static_storage.drive'))
                    ->put($publicFilePath, Storage::disk(Config::get('kusikusi_media.original_storage.drive'))
                    ->get($originalFilePath));
            }
            return Storage::disk(Config::get('kusikusi_media.original_storage.drive'))
                ->response($originalFilePath, null, $headers);
        }

        // Set default values if not set
        data_fill($presetSettings, 'width', 256);  // int
        data_fill($presetSettings, 'height', 256); // int
        data_fill($presetSettings, 'scale', 'cover'); // contain | cover | fill
        data_fill($presetSettings, 'alignment', 'center'); // only if scale is 'cover' or 'contain' with background: top-left | top | top-right | left | center | right | bottom-left | bottom | bottom-right
        data_fill($presetSettings, 'background', null); // only if scale is 'contain': crop | #HEXCODE
        data_fill($presetSettings, 'crop', false); // only if scale is 'contain': true | false
        data_fill($presetSettings, 'quality', 80); // 0 - 100 for jpg | 1 - 8, (bits) for gif | 1 - 8, 24 (bits) for png
        data_fill($presetSettings, 'format', 'jpg'); // jpg | gif | png
        data_fill($presetSettings, 'effects', []); // ['colorize' => [50, 0, 0], 'greyscale' => [] ]


        // The fun
        $filedata = Storage::disk(Config::get('kusikusi_media.original_storage.drive'))->get($originalFilePath);
        $image = Image::make($filedata);
        if ($presetSettings['background'] !== null) {
            $canvas = Image::canvas($image->width(), $image->height(), $presetSettings['background']);
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
            if ($presetSettings['crop']) {
                $image->resizeCanvas($presetSettings['width'], $presetSettings['height'], $presetSettings['alignment'], false, $presetSettings['background']);
            }
        }

        foreach ($presetSettings['effects'] as $key => $value) {
            $image->$key(...$value);
        }

        $image->encode($presetSettings['format'], $presetSettings['quality']);
        Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->put($publicFilePath, $image);

        return $this->getStaticMedium($publicFilePath);
    }

    /**
     * Gets a medium stored in the static drive
     */
    private function getStaticMedium($publicFilePath) {
        return Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->response($publicFilePath);
    }

    /**
     * Uploads a medium
     *
     * @group Media
     * @urlParam entity_id The id of the entity to upload a medium or file
     * @bodyParam file required The file to be uploaded
     * @bodyParam thumb optional An optional file to represent the media, for example a thumb of a video
     * @responseFile responses/entities.index.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload(Request $request, $entity_id)
    {
        $medium = Entity::findOrFail($entity_id);
        function processFile($id, $function, UploadedFile $file)
        {
            $properties = MediumBase::getProperties($file);
            $storageFileName = $function . '.' . $properties['format'];
            Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->deleteDirectory(Config::get('kusikusi_media.original_storage.folder').'/'.$id);
            Storage::disk(Config::get('kusikusi_media.original_storage.drive'))->putFileAs(Config::get('kusikusi_media.original_storage.folder').'/'.$id, $file, $storageFileName);
            return $properties;
        }

        $properties = NULL;
        if ($request->hasFile('thumb') && $request->file('thumb')->isValid()) {
            $properties = processFile($entity_id, 'thumb', $request->file('thumb'));
            $medium->touch();
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $properties = processFile($entity_id, 'file', $request->file('file'));
            if (isset($properties['exif'])) {
                foreach ($properties['exif'] as $prop => $value) {
                    if (Str::startsWith($prop, "UndefinedTag")) {
                        unset($properties['exif'][$prop]);
                    }
                }
            }
            if (empty($medium['properties']) || $medium['properties']->count() === 0) {
                $medium['properties'] = $properties;
            } else {
                $medium['properties'] = array_merge($medium['properties'], $properties);
            }
            $medium->save();
        }
        if ($properties === NULL) {
            abort(400, "No files found in the request or exceed server setting of file size");
        } else {
            return ($properties);
        }
    }
    public function clearStatic(Request $request, $entity_id = null) {
        if ($entity_id) {
            $validator = Validator::make(get_defined_vars(),
                ['entity_id' => 'string|min:1|max:16|regex:/^[A-Za-z0-9_-]+$/|exists:entities,id']
            );
            if ($validator->fails()) {
                return $validator->errors();
            }
        }
        $cleared = MediumModel::clearStatic($entity_id);
        return [
            'cleared' => $cleared
        ];
    }
}