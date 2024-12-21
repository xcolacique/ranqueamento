<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Ranqueamento;
use App\Models\Escolha;
use App\Models\Score;

use App\Services\Utils;

class ScoreController extends Controller
{

    public function show(Ranqueamento $ranqueamento) {
        Gate::authorize('admin');

        // pegando todos estudantes que se candidataram em alguma habilitação
        $candidatos = Escolha::select('user_id')
            ->where('ranqueamento_id',$ranqueamento->id)
            ->distinct()
            ->get();

        // salvando só os estudantes que vão participar desse ranqueamento
        foreach($candidatos as $candidato) {
            $notas = Utils::getNotas($candidato->user->codpes, array_merge(Escolha::disciplinas(),Escolha::disciplinas_segundo()));
            $media = Utils::getMedia($notas);

            $score = Score::where('user_id', $candidato->user_id)
                            ->where('ranqueamento_id',$ranqueamento->id)
                            ->first();

            if(!$score) $score = new Score;

            $score->user_id = $candidato->user_id;
            $score->nota = $media;
            $score->ranqueamento_id = $ranqueamento->id;

            // vamos zerar 
            $score->hab_id_eleita = null;
            $score->prioridade_eleita = null;

            $score->save();
        }

        // classificando para cada habilitação
        foreach($ranqueamento->habs as $hab){
            $vagas = $hab->vagas;
            for ($prioridade = 1; $prioridade <= 7; $prioridade++) { 
                if($vagas == 0 ) break;

                // Candidatos que já foram alocados em alguma habilitação
                $candidatos_alocados = Score::whereNotNull('hab_id_eleita')
                                            ->whereNotNull('prioridade_eleita')
                                            ->pluck('user_id')
                                            ->toArray();
                // todos candidatos inscritos nessa habilitação, nessa prioridade e ainda não alocados
                $inscritos = Escolha::where('hab_id', $hab->id)
                                    ->where('prioridade', $prioridade)
                                    ->whereNotIn('user_id',$candidatos_alocados)
                                    ->get();

                // coleção $inscritos para fazer a classificação               
                $inscritos = $inscritos->map(function($inscrito) use ($hab, $prioridade, $ranqueamento){
                    $score = Score::where('user_id', $inscrito->user_id)
                        ->where('ranqueamento_id',$ranqueamento->id)
                        ->first();

                    return [
                        'user_id'    => $inscrito->user_id,
                        'prioridade' => $inscrito->prioridade,
                        'nota' => $score->nota,
                    ];

                });

                $alocados = $inscritos->where('prioridade', $prioridade)
                                    ->sortByDesc('nota')
                                    ->slice(0,$vagas)
                                    ->pluck('user_id')
                                    ->toArray();

                foreach($alocados as $alocado) {
                    $score = Score::where('user_id', $alocado)
                                    ->where('ranqueamento_id',$ranqueamento->id)
                                    ->first();
                    $score->hab_id_eleita = $hab->id;
                    $score->prioridade_eleita = $prioridade;
                    $score->save();
                }

                $vagas = $vagas - count($alocados);
            }
        }

        $scores = Score::where('ranqueamento_id',$ranqueamento->id)
                            ->orderBy('prioridade_eleita', 'ASC')
                            ->orderBy('nota', 'DESC')
                            ->get();


        return view('scores.show', [
            'scores' => $scores,
            'habs'   => $ranqueamento->habs,
        ]);
    }
}
