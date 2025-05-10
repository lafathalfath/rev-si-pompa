<?php

namespace App\Http\Controllers\PjKecamatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class KecamatanPompaDimanfaatkanController extends Controller
{
    public function index() {
        return view('pj_kecamatan.pompa_dimanfaatkan');
    }
}
