<?php

namespace App\Http\Controllers;

use Kusikusi\Models\Entity;
use Illuminate\Http\Request;
use Kusikusi\Models\Website;

/**
 * Class HtmlController
 * Methods in this class are called when the WebController gets a route, read the entity that route belongs to and
 * determines entity's model.
 * @package Kusikusi\Http\Controllers
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
        /* $result['docs'] = $result['media']->where('properties.isDocument');
        $result['images'] = $result['media']->where('properties.isWebImage'); */
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
            "website" => Website::select('id', 'properties')
                ->withContent($request->lang, ['title'])
                /* ->appendMedium('social') */
                ->find('website'),
            /* "logo" => Medium::select('id', 'properties')
                ->appendContents(['title'], $request->lang)
                ->mediaOf('website', 'logo')
                ->first(),
            "media" => Medium::select('id', 'properties')
                ->appendContents(['title'], $request->lang)
                ->mediaOf($currentEntity->id)
                ->get(), */
            "ancestors" => Entity::select('id', 'model')
                ->ancestorsOf($currentEntity->id)
                ->descendantsOf('website')
                ->orderBy('ancestor.depth', 'desc')
                /* ->appendContents(['title'], $request->lang)
                ->appendRoute($request->lang) */
                ->get(),
            "mainMenu" => Entity::select('id', 'model')
                ->relatedBy('main-menu', 'menu')
                /* ->appendContents(['title'], $request->lang) */
                ->orderBy('related_by.position', 'asc')
                /* ->appendRoute($request->lang) */
                ->isPublished()
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
            ->childrenOf($entity->id)
            /* ->appendContents(['title'], $request->lang)
            ->appendRoute($request->lang)
            ->appendMedium('icon', $request->lang) */
            ->orderBy('position')
            ->isPublished()
            ->get();
        return $children;
    }
}
