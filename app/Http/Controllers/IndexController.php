<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranqueamento;

class IndexController extends Controller
{
    public function index(){

        $ranqueamento = Ranqueamento::where('status',1)->first();

        return view('index',[
            'ranqueamento' => $ranqueamento
        ]);
    }
}
