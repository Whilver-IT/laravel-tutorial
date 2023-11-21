<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    /**
     * メニュー画面表示
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view('menu', ['title' => 'メニュー']);
    }
}