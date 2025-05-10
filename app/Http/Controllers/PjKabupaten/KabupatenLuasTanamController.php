<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KabupatenLuasTanamController extends Controller
{
    public function index() {
        return view('pj_kabupaten.luas_tanam');
    }
}
