<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Models\Declinio;
use App\Models\Ranqueamento;

class EscolhaController extends Controller
{
    public function declinar(Request $request){
        Gate::authorize('ciclo_basico');
        $ranqueamento = Ranqueamento::where('status',1)->first();

        if(!$ranqueamento) {
            $request->session()->flash('alert-danger','Não há ranqueamento ativo');
            return redirect("/");
        }
        
        if($request->declinar==1) {
            $declinio = new Declinio;
            $declinio->user_id = auth()->user()->id;
            $declinio->ranqueamento_id = $ranqueamento->id;
            $declinio->save();
        }

        if($request->declinar==0) {
            $declinio = Declinio::where('user_id', auth()->user()->id)
                                ->where('ranqueamento_id',$ranqueamento->id)->first();
            if($declinio) $declinio->delete();
        }

        return redirect("/");
    }
}
