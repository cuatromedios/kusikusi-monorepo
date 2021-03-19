<?php

namespace App\Http\Controllers;

use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EntityRelationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities = EntityRelation::select()->orderBy('created_at')->get();
        return View::make('entity_relations.index')
           ->with('entities', $entities);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $entities = Entity::select()->orderBy('created_at')->get();
        return View::make('entity_relations.create')
           ->with('entities', $entities)
           ->with('entity_id', $request->input('entity_id'));
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
        $entity = EntityRelation::create($fields);
        return redirect()->route('entities.show', $entity->caller_entity_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $entity = Entity::findOrFail($id);
        $entityWithContents = Entity::select('id', 'model')->with('contents')->findOrFail($id);
        $entityWithContent = Entity::select('id', 'model')->withContent('es', ['title', 'description'])->findOrFail($id);
        //dd($entityWithContent);
        $entityWithRelations = Entity::select('id', 'model')->withContent('es', ['title', 'description'])->with(['entities_related' => function ($q) {$q->withContent('es')->select('id');}])->findOrFail($id);
        return View::make('entities.show')
           ->with('entityWithContents', $entityWithContents)
           ->with('entityWithContent', $entityWithContent)
           ->with('entityWithRelations', $entityWithRelations)
           ->with('entity', $entity);
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
