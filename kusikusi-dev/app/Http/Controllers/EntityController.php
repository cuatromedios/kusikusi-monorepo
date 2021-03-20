<?php

namespace App\Http\Controllers;

use Kusikusi\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EntityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = Entity::select('id', 'properties->format', 'properties->size as properties.size', 'properties->exif->COMPUTED->IsColor')
        ->withContent()
        ->orderBy('created_at')
        ->get();
        return View::make('entities.index')
           ->with('entities', $entities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $entities = Entity::select()->orderBy('created_at')->get();
        return View::make('entities.create')
           ->with('entities', $entities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = $request->except(['_token']);
        $entity = Entity::create($fields);
        return redirect()->route('entities.show', $entity->id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = Entity::select('id', 'properties->p1 as properties.p1', 'properties->p2 as properties.p2')->findOrFail($id);
        $entityWithContents = Entity::select('id', 'model')
            ->withContents('en', 'title')
            ->findOrFail($id);
        $entityWithContent = Entity::select('id', 'model')
            ->withContent('en', 'title')
            ->findOrFail($id);
        $entityWithRelations = Entity::select('id', 'model')
            ->withContent()
            ->with(['entities_related' => function ($q) {
                $q->withContent()
                ->select('id');}])
                ->findOrFail($id);
        $childrenOfEntity = Entity::select('id', 'model')->childrenOf($id)->get();
        $parentOfEntity = Entity::parentOf($id)->first();
        $ancestorsOfEntity = Entity::select('id', 'model')->ancestorsOf($id)->get();
        $descendantsOfEntity = Entity::select('id', 'model')->descendantsOf($id)->get();
        $siblingsOfEntity = Entity::select('id', 'model')->siblingsOf($id)->get();
        $relatedByEntity = Entity::select('id', 'model')->relatedBy($id)->get();
        $relatingEntity = Entity::select('id', 'model')->relating($id)->get();
        $entityWithRoutes = Entity::select('id', 'model')->withRoutes('en')->find($id);
        $entityWithRoute = Entity::select('id', 'model')->withRoute('en', 'main')->find($id);
        return View::make('entities.show')
           ->with('entityWithContents', $entityWithContents)
           ->with('entityWithContent', $entityWithContent)
           ->with('entityWithRelations', $entityWithRelations)
           ->with('entity', $entity)
           ->with('childrenOfEntity', $childrenOfEntity)
           ->with('parentOfEntity', $parentOfEntity)
           ->with('ancestorsOfEntity', $ancestorsOfEntity)
           ->with('descendantsOfEntity', $descendantsOfEntity)
           ->with('siblingsOfEntity', $siblingsOfEntity)
           ->with('relatedByEntity', $relatedByEntity)
           ->with('entityWithRoutes', $entityWithRoutes)
           ->with('entityWithRoute', $entityWithRoute)
           ->with('relatingEntity', $relatingEntity);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
