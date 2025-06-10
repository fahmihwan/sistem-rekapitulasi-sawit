<?php

namespace App\Http\Controllers;

use App\Models\Pembelian_tbs;
use Illuminate\Http\Request;

class TbsController extends Controller
{
    public function index(string $menu)
    {


        $_MAPPING_ID = NULL;
        $_MAPPING_TEXT = NULL;
        if ($menu == 'LAHAN') {
            $_MAPPING_TEXT = "TBS LAHAN";
            $_MAPPING_ID = 1;
        } else if ($menu == 'RUMAH') {
            $_MAPPING_TEXT = "TBS RUMAH";
            $_MAPPING_ID = 2;
        } else if ($menu == 'RAM') {
            $_MAPPING_TEXT = "TBS RAM";
            $_MAPPING_ID = 3;
        } else {
            return "NOT FOUND";
        }

        $data = Pembelian_tbs::where('tbs_type_id', $_MAPPING_ID)->get();


        return view('pages.pembelian_TBS.index', [
            'items' =>  $data,
            'menu' => $_MAPPING_TEXT
        ]);
    }
}
