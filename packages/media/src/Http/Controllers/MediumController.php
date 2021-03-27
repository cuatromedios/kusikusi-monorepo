<?php

namespace Kusikusi\Http\Controllers;

use Kusikusi\Models\Entity;
use Kusikusi\Models\MediumBase;
use Illuminate\Http\Request;
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
        $medium = MediumBase::isPublished()->findOrFail($entity_id);
        $publicFilePath = Config::get('kusikusi_media.original_storage.folder') .'/'. $entity_id .'/'. $preset .'/'. $friendly;
        if (!(Storage::disk(Config::get('kusikusi_media.static_storage.drive'))->exists($publicFilePath))) {
            $medium->processPreset($preset, $friendly);
        } 
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
        $medium = MediumBase::findOrFail($entity_id);
        $properties = NULL;
        if ($request->hasFile('thumb') && $request->file('thumb')->isValid()) {
            $properties = $medium->processUpload('thumb', $request->file('thumb'));
        }
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $properties = $medium->processUpload('file', $request->file('file'));
        }
        if ($properties === NULL) {
            abort(400, "No files found in the request or exceed server setting of file size");
        } else {
            return ($properties);
        }
    }

    public function clearStatic(Request $request, $entity_id = null, $preset = null) {
        $cleared = MediumBase::clearStatic($entity_id, $preset);
        return ['cleared' => $cleared];
    }
}