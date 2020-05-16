<?php

namespace App\Http\Controllers;

use App\Models\Entity;
use App\Models\Medium;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;

/**
 * Class HtmlController
 * Methods in this class are called when the WebController gets a route, read the entity that route belongs to and
 * determines entity's model.
 * @package App\Http\Controllers
 */
class HtmlController extends Controller
{
    public function home(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.'.$entity->view, $result);
    }
    public function section(Request $request, Entity $entity)
    {
        $result = $this->common($request, $entity);
        $result['children'] = $this->children($request, $entity);
        return view('html.'.$entity->view, $result);
    }

    public function page(Request $request, Entity $entity, $lang)
    {
        $result = $this->common($request, $entity);
        return view('html.'.$entity->view, $result);
    }
    public function product(Request $request, Entity $entity, $lang)
    {
        $result = $this->common($request, $entity);
        return view('html.'.$entity->view, $result);
    }
    public function error(Request $request, $status)
    {
        $result = [ "status" => $status ];
        $result['lang'] = $request->lang;
        return view('html.error', $result);
    }

    /**
     * The common method can be called by any other method to receive the commonly required properties to be send
     * to the view, for example, the current language, all the fields of the entity, the media and ancestors.
     * @param Request $request
     * @param Entity $currentEntity
     * @return array
     */
    private function common(Request $request, Entity $currentEntity) {
        $result = [
            "lang" => $request->lang,
            "entity" => $currentEntity,
            "website" => Entity::select('id')
                ->appendContents(['title'])
                ->appendMedium('social')
                ->find('website'),
            "media" => Medium::select('id')
                ->appendContents(['title'], $request->lang)
                ->mediaOf($currentEntity->id)
                ->get(),
            "ancestors" => Entity::select('id', 'model')
                ->ancestorOf($currentEntity->id)
                ->descendantOf('website')
                ->orderBy('ancestor_relation_depth', 'desc')
                ->appendContents(['title'], $request->lang)
                ->appendRoute($request->lang)
                ->get()
        ];
        return $result;
    }

    /**
     * The children private method returns the children of an entity, with thte title content, its route and a medium
     * path if it has one.
     * @param Request $request
     * @param Entity $entity
     * @return mixed
     */
    private function children(Request $request, Entity $entity) {
        $children = Entity::select('id', 'model')
            ->childOf($entity->id)
            ->appendContents(['title'], $request->lang)
            ->appendRoute($request->lang)
            ->appendMedium('icon')
            ->orderBy('position')
            ->orderBy('title')
            ->get();
        return $children;
    }
}
