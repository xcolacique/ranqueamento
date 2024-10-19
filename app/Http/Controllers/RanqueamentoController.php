<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranqueamento;

class RanqueamentoController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('admin');

        return view ('ranqueamentos.index',[
            'ranqueamentos' => Ranqueamento::all(),
        ]);
    }

    public function create()
    {
        $this->authorize('admin');
        return view('ranqueamentos.create',[]);  
    }

    public function store(Request $request)
    {
        $this->authorize('admin');
        $validated = $request->validate([
            'tipo'   => 'required',
            'ano'    => 'required',
            'status' => 'required'
        ]);
        $ranqueamento = Ranqueamento::create($validated);
        $ranqueamento->save();
        return redirect("/ranqueamentos"); 
    }

    public function show(Ranqueamento $ranqueamento)
    {
        $this->authorize('admin');
        return view('ranqueamentos.show',[
            'ranqueamento' => $ranqueamento,
        ]); 
    }

    public function edit(Country $country)
    {
        $this->authorize('admin');
        return view('ranqueamentos.edit',[
            'ranqueamento' => $ranqueamento,
        ]); 
    }
    public function update(Request $request, Ranqueamento $ranqueamento)
    {
        $this->authorize('admin');
        $validated = $request->validate([
            'tipo'   => 'required',
            'ano'    => 'required',
            'status' => 'required'
        ]);
        $ranqueamento->update($validated);
        $ranqueamento->save();
        return redirect("/ranqueamentos"); 
    }

    public function destroy(Country $country)
    {
        // não vamos permitir por enquanto
        dd('Não permitido');
        $this->authorize('admin');

        $ranqueamento->delete();
        request()->session()->flash('alert-info','Ranqueamento excluído com sucesso.');
        return redirect("/ranqueamentos"); 
    }
}
