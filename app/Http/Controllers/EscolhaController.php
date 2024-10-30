<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Declinio;

class EscolhaController extends Controller
{
    public function declinio(Request $request){
        Gate::authorize('ciclo_basico');
        
    }
}
