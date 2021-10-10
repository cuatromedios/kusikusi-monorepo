<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Http\Testing\MimeType;

class AdminController extends Controller
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
     * Returns an static file if exists or index.html.
     *
     * @group Admin
     * @urlParam path.
     * @return Response
     */
    public function any(Request $request, $path = "/")
    {
        $base = base_path('vendor/kusikusi/admin/dist/');
        $file = Str::afterLast($path, "/");
        $isFile = Str::afterLast($file, ".") !== $file;

        if ($isFile) {
            return response()->file($base.$path, ['Content-Type' => MimeType::from($file)]);
        } else {
            return response()->file($base.'index.html', ['Content-Type' => 'text/html']);
        }
    }
}
