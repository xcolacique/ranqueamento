<?php

namespace App\Services;

use App\Models\Declinio;
use App\Models\Escolha;
use App\Models\User;
use App\Models\Score;
use App\Models\Ranqueamento;
use Illuminate\Database\Eloquent\Builder;

class HabilitacaoService
{
    public static function options(Ranqueamento $ranqueamento)
    {
        $declinios = Declinio::where('ranqueamento_id', $ranqueamento->id)->pluck('user_id');

        $escolhas = Escolha::select(['prioridade','hab_id', 'user_id'])
            ->with(['hab:id,nomhab,perhab'])
            ->where('ranqueamento_id', $ranqueamento->id)
            ->orderBy('user_id')
            ->orderBy('prioridade')
            ->get();

        $alunos = User::wherehas('escolhas', function (Builder $query) use ($ranqueamento) {
                $query->where('ranqueamento_id', $ranqueamento->id);
            })
            ->select(['id','codpes','name','email'])
            ->orderBy('name')
            ->get()
            ->map(function($aluno) use($declinios, $escolhas, $ranqueamento) {

                $score = Score::where('codpes',$aluno->codpes)
                                ->where('ranqueamento_id',$ranqueamento->id)
                                ->first();
                $aluno = [
                    'id' => $aluno->id,
                    'codpes' => $aluno->codpes,
                    'name' => $aluno->name,
                    'email' => $aluno->email,
                    'media' => 0,
                    'classificacao' => '',
                    'prioridade_classificacao' => '',
                    'posicao' => '',
                    'declinou' => $declinios->contains($aluno->id) ? 'sim' : 'não',
                ];


                if($score) {
                    $aluno['media'] = $score->nota;
                    $aluno['classificacao'] = $score->hab ? $score->hab->nomhab: $score->hab;
                    $aluno['prioridade_classificacao'] = $score->prioridade_eleita;
                    $aluno['posicao'] = $score->posicao;
                }

                $habilitacoes = $escolhas->where('user_id', $aluno['id']);

                for ($prioridade = 1; $prioridade <= $ranqueamento->max; $prioridade++) {
                    $habilitacao = $habilitacoes->where('prioridade', $prioridade)->first();
                    if($habilitacao) {
                        $aluno['nomhab' . $prioridade] = $habilitacao->hab->nomhab . ' - ' . $habilitacao->hab->perhab;
                    } else {
                        $aluno['nomhab' . $prioridade] = '-';
                    }
                }

                return $aluno;

            });

        return $alunos;
    }

    public static function headings(Ranqueamento $ranqueamento) {

        $heading = ['id','Número USP','Nome','E-mail','Média','Classificação','Prioridade Eleita','Posição','Declinou do Português?'];

        for ($prioridade = 1; $prioridade <= $ranqueamento->max; $prioridade++) {
            $heading[] = 'Opção ' . $prioridade; 
        }

        return $heading;
    }
}
