<?php

namespace App\Http\Controllers\PjKabupaten;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KabupatenPompaUsulanController extends Controller
{
    public function index() {
        return view('pj_kabupaten.pompa_usulan');
    }
}
