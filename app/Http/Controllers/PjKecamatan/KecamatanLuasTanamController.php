<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KecamatanLuasTanamController extends Controller
{
    public function index() {
        return view('pj_kecamatan.luas_tanam');
    }
}
