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
        $entities = Entity::select()->withContent()->orderBy('created_at')->get();
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
        $entity = Entity::findOrFail($id);
        $entityWithContents = Entity::select('id', 'model')->with('contents')->findOrFail($id);
        $entityWithContent = Entity::select('id', 'model')
            ->withContent('es', ['title', 'summary'])
            ->findOrFail($id);
        $entityWithRelations = Entity::select('id', 'model')
            ->withContent('es')
            ->with(['entities_related' => function ($q) {
                $q->withContent('es')->select('id');}])
            ->findOrFail($id);
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
