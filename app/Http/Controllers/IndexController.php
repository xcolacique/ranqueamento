<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Uspdev\Replicado\Graduacao;

class IndexController extends Controller
{
    public function index(){
        dd(Graduacao::obterCursosHabilitacoes(8));
        return view('index');
    }
}
