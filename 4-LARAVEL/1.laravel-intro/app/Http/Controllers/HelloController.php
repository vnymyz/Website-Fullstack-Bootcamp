<?php

namespace App\Http\Controllers;

class HelloController extends Controller
{
    // method index untuk menampilkan halaman hello world
    public function index()
    {
        return view('hello', ['name' => 'Vanya']);
    }

    // method baru hello {name} untuk menampilkan nama yang berbeda atau dinamis
    public function show($name)
    {
        return view('hello', ['name' => $name]);
    }
}
