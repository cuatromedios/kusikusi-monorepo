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
use Illuminate\Http\Testing\MimeType;

class WebsiteController extends Controller
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
    public function any(Request $request, $path = "")
    {
        $format = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $originalFormat = $format;
        if ($format === '') $format = 'html';
        if (!in_array($format, Config::get('kusikusi_website.formats', ['html']))) {
            $controller = new HtmlController();
            return ($controller->error($request, 404));
        }
        $path = "/".Str::beforeLast($path, '.');
        $filename = Str::afterLast($path, '/');
        $staticPath = "$path.$format";

        // Send the file stored in the static folder if it exist
        $staticStorage = Config::get('kusikusi_website.static_storage.drive');
        if (!env('APP_DEBUG', false) && Storage::disk($staticStorage)->exists($staticPath)) {
            $mimeType =  MimeType::from($staticPath);
            $headers = ['Content-Type' => $mimeType];
            return Storage::disk($staticStorage)->response($staticPath, null, $headers);
        }
        // Search for the entity is being called by its url, ignore inactive and soft deleted.
        $searchRouteResult = EntityRoute::where('path', $path)->first();
        if (!$searchRouteResult) {
            $defaultLang = config('kusikusi_website.langs', [''])[0];
            App::setLocale($defaultLang);
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
                return redirect($redirect->path . ($originalFormat !== '' ? '.'.$originalFormat : ''), $status || 301);
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
            return ($controller->error($request, 501));
        }
        $controller = new $controllerClassName;
        if (method_exists($controller, $model_name)) {
            $view = $controller->$model_name($request, $entity, $lang);
            if (Config::get('kusikusi_website.static_generation', 'none') === 'lazy') {
                $render = $view->render();
                if ($path !== '/' || $path === '') {
                    Storage::disk($staticStorage)->put($staticPath, $render);
                }
                if ($format === 'html') {
                    Storage::disk($staticStorage)->put("$path/index.$format", $render);
                }
            }
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
