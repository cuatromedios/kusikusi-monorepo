<?php

namespace App\Http\Controllers;

use Kusikusi\Models\EntityArchive;
use Kusikusi\Models\Entity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class EntityArchiveController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $entities_archives = EntityArchive::select()->orderBy('kind')->get();
        $archives_payload = EntityArchive::select()->orderBy('created_at')->get();
        $entity = Entity::select()->orderBy('created_at')->with('entities_related')->get();
        return View::make('entities_archives.index')
           ->with('entities_archives', $entities_archives)
           ->with('archives_payload', $archives_payload)
           ->with('entity', $entity);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $entities = Entity::select()->orderBy('created_at')->get();
        return View::make('entities_archives.create')
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
        $entity_archive = EntityArchive::archive($fields['entity_id'], $fields['kind']);
        return redirect()->route('entities_archives.show', $entity_archive['entity_id']);
    }

    /**
     * Show the form for restoring a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function restore()
    {
        $entities_archives = EntityArchive::select()->orderBy('created_at')->distinct()->get();
        $entities = Entity::select()->orderBy('created_at')->get();
        return View::make('entities_archives.restore')
           ->with('entities_archives', $entities_archives)
           ->with('entities', $entities);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function restore_store(Request $request)
    {
        $fields = $request->except(['_token']);
        $entity = EntityArchive::updateFromArchive($fields['entity_id'], $fields['archive_id']);
        return redirect()->route('entities.show', $entity['id']);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $entity_id
     * @return \Illuminate\Http\Response
     */
    public function show($entity_id)
    {
        $entity = EntityArchive::where('entity_id', $entity_id)->firstOrFail();
        $entity_archives = EntityArchive::where('entity_id', $entity_id)->firstOrFail();
        $archive_payload = EntityArchive::select('payload')->where('entity_id', $entity_id)->firstOrFail();
        return View::make('entities_archives.show')
           ->with('entity_archive', $entity_archives)
           ->with('archive_payload', $archive_payload)
           ->with('entity', $entity);
           
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit()
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
