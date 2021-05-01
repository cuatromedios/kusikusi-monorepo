<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Create a new controller instance
     * 
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validate = $request->validate([
            "login" => "required|string",
            "password" => "required|string"
        ]);
        $auth = User::authenticate($request->input('login'), $request->input('password'), 'login');
        return $auth;
    }
}
