<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


use App\Http\Requests;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = User::with('company', 'profile')->where('id', '=', Auth::user()->id)->first();

        return view('account', compact('user'));
    }

    
}
