<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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