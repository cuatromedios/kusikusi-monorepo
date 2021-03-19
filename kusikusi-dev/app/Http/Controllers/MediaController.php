<?php

namespace App\Http\Controllers;

use App\Models\Medium;
use Kusikusi\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Database\Eloquent\Builder;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = Medium::select('id', 'model', 'properties')
        ->withContent('en')
        ->orderBy('created_at')
        ->get();
        $entitiesWithMedia = Entity::select('id', 'model')
        ->withMedia('icon', ['id', 'model'], 'en')
        ->whereHas('medium', function (Builder $query) {
            $query->whereJsonContains('tags', 'icon');
        })
        ->where('model', '!=', 'Medium')
        ->get();
        $entitiesWithMedium = Entity::select('id', 'model')
        ->where('model', '!=', 'Medium')
        ->whereHasMediumWithTag('icon')
        ->withMedium('icon', ['id', 'model'], 'en', ['title'])
        ->get();
        return View::make('media.index')
           ->with('entities', $entities)
           ->with('with_media', $entitiesWithMedia)
           ->with('with_medium', $entitiesWithMedium);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = Medium::withContent()->findOrFail($id);
        return View::make('media.show')
           ->with('entity', $entity);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

}
