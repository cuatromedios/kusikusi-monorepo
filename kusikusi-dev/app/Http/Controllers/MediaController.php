<?php

namespace App\Http\Controllers;

use App\Models\Medium;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

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
        return View::make('media.index')
           ->with('entities', $entities);
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
