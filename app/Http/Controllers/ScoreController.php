<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Ranqueamento;
use App\Models\Escolha;
use App\Models\Score;
use App\Models\Declinio;
use App\Models\Hab;

use App\Services\Utils;
use App\Exports\CsvExportScores;
use Maatwebsite\Excel\Excel;

class ScoreController extends Controller
{

    public function update(Ranqueamento $ranqueamento) {
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
            $score->codpes = $candidato->user->codpes;
            $score->codpgm = Utils::get_codpgm($candidato->user->codpes);
            $score->nota = $media;
            $score->ranqueamento_id = $ranqueamento->id;

            // vamos zerar 
            $score->hab_id_eleita = null;
            $score->prioridade_eleita = null;
            $score->save();
        }

        // Controlando as vagas ainda disponíveis para cada habilitação
        $vagas = [];
        $posicao = [];
        foreach($ranqueamento->habs as $hab){
            $vagas[$hab->id] = $hab->vagas;
            $posicao[$hab->id] = 1;
        }

        // para cada candidato, da maior média para a menor, vamos alocando nas habilitações
        foreach(Score::orderBy('nota','DESC')->get() as $score){
            // vamos varrer cada prioridade e verificar se o aluno tem nota suficiente para entrar em alguma habilitação
            for($prioridade = 1; $prioridade <= 7; $prioridade++) {

                // Candidatos que já foram alocados em alguma habilitação e vamos ignorar
                $candidatos_alocados = Score::whereNotNull('hab_id_eleita')
                                            ->whereNotNull('prioridade_eleita')
                                            ->pluck('user_id')
                                            ->toArray();

                // Escolhas para o cadidato em questão, caso ele ainda não tenha sido alocado
                $escolha = Escolha::where('ranqueamento_id',$ranqueamento->id)
                                    ->where('user_id', $score->user_id)
                                    ->where('prioridade', $prioridade)
                                    ->whereNotIn('user_id',$candidatos_alocados)
                                    ->first();

                // Se o candidado escolheu uma habilitação nessa prioridade e ainda não tem habilitação
                // E se ainda tem vaga nessa habilitação
                if($escolha && $vagas[$escolha->hab->id]>0) {
                    $score->hab_id_eleita = $escolha->hab->id;
                    $score->prioridade_eleita = $prioridade;
                    $score->posicao = $posicao[$escolha->hab->id];
                    $score->save();
                    $vagas[$escolha->hab->id] = $vagas[$escolha->hab->id] - 1;
                    $posicao[$escolha->hab->id] = $posicao[$escolha->hab->id] + 1;
                }
            }
        }

        $scores = Score::where('ranqueamento_id',$ranqueamento->id)
                        ->orderBy('nota','DESC')
                        ->get();
        
        // Acertando as declinações do português
        foreach($scores as $score){
            $periodo = Utils::periodo($score->user->codpes);

            // aqueles que não foram alocados em disciplina alguma, ficarão somente com o português
            if(is_null($score->hab_id_eleita)){
                if($periodo == 'matutino') $score->codhab_jupiterweb = 202;
                else $score->codhab_jupiterweb = 204;
            } else {
                $declinio = Declinio::where('user_id',$score->user_id)
                                    ->where('ranqueamento_id', $ranqueamento->id)
                                    ->first();
                // se o aluno declinou do português e foi selecionado no ranqueamento
                if($declinio) {
                    $score->codhab_jupiterweb = $score->hab->codhab;
                } else {
                    // se o aluno não declinou do português e foi selecionado no ranqueamento
                    $score->codhab_jupiterweb = Hab::mapeamento()[$score->hab->codhab];
                }
            }
            $score->save();
        }
        
        return view('scores.show', [
            'scores' => $scores,
            'ranqueamento'   => $ranqueamento,
        ]);
    }

    public function show(Ranqueamento $ranqueamento) {
        Gate::authorize('admin');

        $scores = Score::where('ranqueamento_id',$ranqueamento->id)
                        ->orderBy('nota','DESC')
                        ->get();

        return view('scores.show', [
            'scores' => $scores,
            'ranqueamento'   => $ranqueamento,
        ]);
    }

    public function csv(Excel $excel, Ranqueamento $ranqueamento){

        $data = Score::where('ranqueamento_id',$ranqueamento->id)->get();

        // colunas fixas
        foreach($data as $item){
            $item['codcur'] = 8051;
            $item['dtaini'] = "2025-01-01 00:00:00.000";
        }

        $data = $data->map
                    ->only(['codpes', 'codpgm', 'codcur', 'codhab_jupiterweb', 'dtaini'])
                    ->toArray();

    
        $headings = ['codpes','codgpm','codcur','codhab','dtaini'];

        $export = new CsvExportScores($data, $headings);
        return $excel->download($export, 'ranqueamento.csv', Excel::CSV, ['Content-Type' => 'text/csv']);

    }

}
