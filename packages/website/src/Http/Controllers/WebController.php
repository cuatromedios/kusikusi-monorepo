<?php

namespace Kusikusi\Http\Controllers;

use App\Http\Controllers\HtmlController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kusikusi\Models\EntityRoute;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Kusikusi\Models\Website;
use Kusikusi\Models\Entity;
use Illuminate\Support\Facades\Validator;
use Mimey\MimeTypes;

class WebController extends Controller
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
     * Locates an entity based on the url, and returns the HTML view of that entity as a webpage
     *
     * @group Web
     * @param $request \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function any(Request $request)
    {
        $path = $request->path() == '/' ? '/' : '/' . $request->path();
        $originalExtension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if ($format === '') {
            $format = 'html';
            $static_path = Str::finish($path, '/').'index.html';
        } else {
            $path = substr($path, 0, strrpos($path, "."));
            $static_path = $path.'.'.$format;
        }
        $path = preg_replace('/\/index$/', '', $path);
        if ($path === '') $path = '/';
        $filename = strtolower(pathinfo($path, PATHINFO_FILENAME));

        // Send the file stored in the static folder if it exist
        /* if ($exists = Storage::disk('views_processed')->exists($static_path)  && (!env('APP_DEBUG', false) || $format !== 'html')) {
            $mimes = new MimeTypes;
            $mimeType =  $mimes->getMimeType($format);
            $headers = ['Content-Type' => $mimeType];
            return Storage::disk('views_processed')->response($static_path, null, $headers);
        } */

        // Search for the entity is being called by its url, ignore inactive and soft deleted.
        $defaultLang = config('cms.langs', [''])[0];
        App::setLocale($defaultLang);
        $searchRouteResult = EntityRoute::where('path', $path)->first();
        if (!$searchRouteResult) {
            $request->lang = $defaultLang;
            $controller = new HtmlController();
            return ($controller->error($request, 404));
        }
        switch ($searchRouteResult->kind) {
            case 'temporal_redirect':
                $status = 307;
            case 'permanent_redirect':
                $redirect = EntityRoute::where('entity_id', $searchRouteResult->entity_id)
                    ->where('lang', $searchRouteResult->lang)
                    ->where('kind', 'main')
                    ->first();
                    return redirect($redirect->path . ($originalExtension !== '' ? '.'.$originalExtension : ''), $status || 301);
                break;
            case 'alias':
            case 'main':
                break;
        }
        // Select an entity with its properties
        $lang = $searchRouteResult->lang;
        App::setLocale($lang);

        // Get the model class name from App or Kusikusi
        $modelClassName = Entity::getEntityClassName($searchRouteResult->entity_model);
    
        $entity = $modelClassName::select("*")
            ->where("id", $searchRouteResult->entity_id)
            ->isPublished()
            ->withContent($lang)
            /* ->appendProperties()
            ->appendRoute($lang)
            ->appendMedium('social') */
            ->with('entities_related')
            ->with('routes')
            ->first();
        if (!$entity) {
            $controller = new HtmlController();
            return ($controller->error($request, 404));
        }
        $request->request->add(['lang' => $lang]);
        $model_name = $entity->model;
        $controllerClassName = "App\\Http\\Controllers\\" . ucfirst($format) . 'Controller';
        if(!class_exists($controllerClassName)) {
            $controller = new HtmlController;
            return ($controller->error($request, 404));
        }
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            $view = $controller->$model_name($request, $entity, $lang);
            /* if (Config::get('cms.static_generation', 'lazy') === 'lazy') {
                $modelInstance = new $modelClassName;
                if ($modelInstance->getCacheViewsAs()) {
                    $render = $view->render();
                    if ($modelInstance->getCacheViewsAs() === 'directory' || $path === '/'  || $path === '') {
                        $cachePath = "$path/index.$format";
                        if ($path !== '/'  && $path !== '') {
                            Storage::disk('views_processed')->put($path.'.'.$format, $render);
                        }
                    } else {
                        $cachePath = "$path.$format";
                    }
                    Storage::disk('views_processed')->put($cachePath, $render);
                }
            } */
            return $view;
        } else {
            return ($controller->error($request, 501));
        }
    }

    public function clearStatic(Request $request, $entity_id = null) {
        if ($entity_id) {
            $validator = Validator::make(get_defined_vars(),
                ['entity_id' => 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]+$/|exists:entities,id']
            );
            if ($validator->fails()) {
                return $validator->errors();
            }
        }
        $cleared = Website::clearStatic($entity_id);
        return [
            'cleared' => $cleared
        ];
    }

    public function recreateStatic() {
        return [ "recreated" => Website::recreateStatic() ];
    }
}
