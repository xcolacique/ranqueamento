<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Models\Ranqueamento;

class RanqueamentoController extends Controller
{

    public function index(Request $request)
    {
        Gate::authorize('admin');

        return view ('ranqueamentos.index',[
            'ranqueamentos' => Ranqueamento::all(),
        ]);
    }

    public function create()
    {
        Gate::authorize('admin');
        return view('ranqueamentos.create'); 
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'ano'    => 'required|integer', # entre 2024 e 2100?
            'tipo'   => 'required', # TODO: validar somente ingressantes e reranqueamento
            'status' => 'nullable' # só pode se nulo ou 1
        ]);

        // Se o status não foi ativado, será null
        if($request->status == null) $request->status = 0;

        // se foi ativado, todos demais ranqueamentos serão desativados
        if($request->status == 1) {
            foreach(Ranqueamento::all() as $r){
                $r->status = 0;
                $r->save();
            }
        }

        $ranqueamento = new Ranqueamento;
        $ranqueamento->ano = $request->ano;
        $ranqueamento->tipo = $request->tipo;
        $ranqueamento->status = $request->status;
        $ranqueamento->save();
        return redirect("/ranqueamentos"); 
    }

    public function show(Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');
        return view('ranqueamentos.show',[
            'ranqueamento' => $ranqueamento,
        ]); 
    }

    public function edit(Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');
        return view('ranqueamentos.edit',[
            'ranqueamento' => $ranqueamento,
        ]); 
    }
    public function update(Request $request, Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');

        $request->validate([
            'ano'    => 'required|integer', # entre 2024 e 2100?
            'tipo'   => 'required', # TODO: validar somente ingressantes e reranqueamento
            'status' => 'nullable' # só pode se nulo ou 1
        ]);

        // Se o status não foi ativado, será null
        if($request->status == null) $request->status = 0;

        // se foi ativado, todos demais ranqueamentos serão desativados
        if($request->status == 1) {
            foreach(Ranqueamento::all() as $r){
                $r->status = 0;
                $r->save();
            }
        }

        $ranqueamento->ano = $request->ano;
        $ranqueamento->tipo = $request->tipo;
        $ranqueamento->status = $request->status;
        $ranqueamento->save();
        return redirect("/ranqueamentos"); 
    }

    public function destroy(Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');
        // não vamos permitir por enquanto
        dd('Não permitido');
        
        $ranqueamento->delete();
        request()->session()->flash('alert-info','Ranqueamento excluído com sucesso.');
        return redirect("/ranqueamentos"); 
    }
}
