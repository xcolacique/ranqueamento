<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranqueamento;
use App\Models\Score;
use App\Models\Escolha;
use App\Models\Hab;


class IndexController extends Controller
{
    public function index(){

       /* $ranqueamento = Ranqueamento::where('status',1)->first();

        return view('index',[
            'ranqueamento' => $ranqueamento
        ]);*/

        //$exibirResultados = true;

        $user = auth()->user();
        // ranqueamento ativo
        $ranqueamentoAtivo = Ranqueamento::where('status', 1)->first();

        // último ranqueamento encerrado
        $ranqueamentoFinalizado = Ranqueamento::where('status', 0)
            ->where('id', '!=', 0)  // Resultado ficará pronto em breve
            ->orderByDesc('ano')
            ->orderByDesc('id')
            ->first();

        $resultado = null;
        $notaCorte = null;
        

        if ($user && $ranqueamentoFinalizado) {
            $resultado = Score::where('user_id', $user->id)
                ->where('ranqueamento_id', $ranqueamentoFinalizado->id)
                ->first();

        if ($resultado) {
            // Se foi classificado, usa hab_id_eleita
            if ($resultado->hab_id_eleita) {
                $habId = $resultado->hab_id_eleita;
                
                if (!$resultado->hab) {
                    $resultado->load('hab');
                }
            } 
            // Se não foi classificado, busca a escolha de prioridade 1
            else {
                $escolha = Escolha::where('ranqueamento_id', $ranqueamentoFinalizado->id)
                    ->where('user_id', $user->id)
                    ->where('prioridade', 1)
                    ->first();
                
                if ($escolha) {
                    $habId = $escolha->hab_id;
                    $resultado->hab = Hab::find($habId);
                }
            }
            
            // Calcular nota de corte se tiver habilitação
            if (isset($habId) && $habId && $resultado->hab) {
                $ultimoClassificado = Score::where('ranqueamento_id', $ranqueamentoFinalizado->id)
                    ->where('hab_id_eleita', $habId)
                    ->where('posicao', $resultado->hab->vagas)
                    ->first();
                
                $notaCorte = $ultimoClassificado ? $ultimoClassificado->nota : null;
            }
        }
            }   
     


        return view('index', [
            'ranqueamento' => $ranqueamentoAtivo,
            'ranqueamentoFinalizado' => $ranqueamentoFinalizado,
            'resultado' => $resultado,
            'notaCorte' => $notaCorte,
        ]);
    }
}
