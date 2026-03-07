<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class SmeltingWebController extends Controller
{
    public function index()
    {
        return view('admin.mes.smelting.index');
    }

    public function create()
    {
        return view('admin.mes.smelting.form');
    }

    public function edit(int $id)
    {
        return view('admin.mes.smelting.form');
    }
}