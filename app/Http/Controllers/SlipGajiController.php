<?php

namespace App\Http\Controllers;

use App\Models\M_karyawan;
use Illuminate\Http\Request;

class SlipGajiController extends Controller
{
    public function index()
    {
        return view('pages.slip-gaji.index', [
            'items' =>  M_karyawan::latest()->get()
        ]);
    }
}
