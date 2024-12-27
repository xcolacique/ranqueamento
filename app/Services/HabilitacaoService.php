<?php

namespace App\Services;

use App\Models\Declinio;
use App\Models\Escolha;
use App\Models\User;
use App\Models\Score;
use Illuminate\Database\Eloquent\Builder;

class HabilitacaoService
{
    public static function options(int $ranqueamento_id)
    {
        $declinios = Declinio::where('ranqueamento_id', $ranqueamento_id)->pluck('user_id');

        $escolhas = Escolha::select(['prioridade','hab_id', 'user_id'])
            ->with(['hab:id,nomhab,perhab'])
            ->where('ranqueamento_id', $ranqueamento_id)
            ->orderBy('user_id')
            ->orderBy('prioridade')
            ->get();

        $alunos = User::wherehas('escolhas', function (Builder $query) use ($ranqueamento_id) {
                $query->where('ranqueamento_id', $ranqueamento_id);
            })
            ->select(['id','codpes','name'])
            ->orderBy('name')
            ->get()
            ->map(function($aluno) use($declinios, $escolhas, $ranqueamento_id) {

                $score = Score::where('codpes',$aluno->codpes)
                                ->where('ranqueamento_id',$ranqueamento_id)
                                ->first();

                $aluno = [
                    'id' => $aluno->id,
                    'codpes' => $aluno->codpes,
                    'name' => $aluno->name,
                    'declinou' => $declinios->contains($aluno->id) ? 'sim' : 'não',
                    'media' => $score->nota,
                    'classificacao' => $score->hab ? $score->hab->nomhab: '',
                    'prioridade_classificacao' => $score->prioridade_eleita
                ];

                $habilitacoes = $escolhas->where('user_id', $aluno['id']);

                for ($prioridade = 1; $prioridade <= 7; $prioridade++) {
                    $habilitacao = $habilitacoes->where('prioridade', $prioridade)->first();
                    if($habilitacao) {
                        $aluno['nomhab' . $prioridade] = $habilitacao->hab->nomhab . ' - ' . $habilitacao->hab->perhab;
                        if($prioridade == $score->prioridade_eleita) {
                            $aluno['nomhab' . $prioridade] = "<span style='color:red;'>{$aluno['nomhab' . $prioridade]}</span>";
                        }
                    } else {
                        $aluno['nomhab' . $prioridade] = '-';
                    }

                }

                return $aluno;

            });

        return $alunos;
    }

    public static function headings() {
        return [
            'id',
            'Número USP',
            'Nome',
            'Declinou do Português?',
            'Média',
            'Classificação',
            'Opção Eleita',
            'Opção 1',
            'Opção 2',
            'Opção 3',
            'Opção 4',
            'Opção 5',
            'Opção 6',
            'Opção 7'
        ];
    }
}
