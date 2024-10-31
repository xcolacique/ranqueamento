<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Ranqueamento;
use App\Models\Hab;
use App\Rules\CodpesRule;
use App\Service\Utils;

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
        return view('ranqueamentos.create',[
            'habs' => Utils::lista_habs(),
        ]); 
    }

    public function store(Request $request)
    {
        Gate::authorize('admin');

        $request->validate([
            'ano'        => [ 'required', 'between:2024,2100', 'integer', 
                               Rule::unique('ranqueamentos', 'ano')->where('tipo', $request->tipo)
                            ], 
            'tipo'       => 'required', # TODO: validar somente ingressantes e reranqueamento
            'status'     => 'nullable', # só pode se nulo ou 1,
            'permitidos' => ['nullable', new CodpesRule]
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
        $ranqueamento->permitidos = Utils::limpa_string_de_codpes($request->permitidos);
        $ranqueamento->save();

         // procedimento para salvar as vagas em cada habilitação
         foreach($request->all() as $key=>$value) {
            if(str_starts_with($key,'hab-')) {
                $codhab = str_replace('hab-', '', $key);
                $hab_replicado = Utils::get_hab($codhab);
                if($hab_replicado) {
                    $hab = new Hab;
                    $hab->codhab = $codhab;
                    $hab->nomhab = $hab_replicado['nomhab'];
                    $hab->perhab = $hab_replicado['perhab'];
                    if(is_null($value) || empty($value)) $value=0;
                    $hab->vagas = (int)$value;
                    $hab->ranqueamento_id = $ranqueamento->id;
                    // verificando se tem checkbox
                    if(array_key_exists("checkbox-{$codhab}", $request->all())){
                        $hab->permite_ambos_periodos = 1;
                    } else {
                        $hab->permite_ambos_periodos = 0;
                    }
                    $hab->save();
                }
            }
         }

        return redirect("/ranqueamentos"); 
    }

    public function show(Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');
        $habs = Hab::where('ranqueamento_id',$ranqueamento->id)->get();
        return view('ranqueamentos.show',[
            'ranqueamento' => $ranqueamento,
            'habs'         => $habs
        ]); 
    }

    public function edit(Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');
        $habs = Hab::where('ranqueamento_id',$ranqueamento->id)->get();
        return view('ranqueamentos.edit',[
            'ranqueamento' => $ranqueamento,
            'habs'         => $habs
        ]); 
    }
    public function update(Request $request, Ranqueamento $ranqueamento)
    {
        Gate::authorize('admin');

        $request->validate([
            'ano'        => [ 'required', 'between:2024,2100', 'integer', 
                               Rule::unique('ranqueamentos', 'ano')->where('tipo', $request->tipo)
                               ->ignore($ranqueamento->id)
                            ], 
            'tipo'   => 'required', # TODO: validar somente ingressantes e reranqueamento
            'status' => 'nullable', # só pode se nulo ou 1
            'permitidos' => ['nullable', new CodpesRule]
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
        $ranqueamento->permitidos = Utils::limpa_string_de_codpes($request->permitidos);
        $ranqueamento->save();

        // procedimento para salvar as vagas em cada habilitação
        foreach($request->all() as $key=>$value) {
            if(str_starts_with($key,'hab-')) {
                $codhab = str_replace('hab-', '', $key);
                $hab = Hab::where('codhab', $codhab)->where('ranqueamento_id',$ranqueamento->id)->first();
                if(is_null($value) || empty($value)) $value=0;
                $hab->vagas = (int)$value;
                // verificando se tem checkbox
                if(array_key_exists("checkbox-{$codhab}", $request->all())){
                    $hab->permite_ambos_periodos = 1;
                } else {
                    $hab->permite_ambos_periodos = 0;
                }
                $hab->save();
            }
        }

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
