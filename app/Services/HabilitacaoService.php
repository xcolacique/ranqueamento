<?php

namespace App\Services;

use App\Models\Declinio;
use App\Models\Escolha;
use App\Models\User;
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
            ->map(function($aluno) use($declinios, $escolhas) {

                $notas = Utils::getNotas($aluno->codpes, array_merge(Escolha::disciplinas(),Escolha::disciplinas_segundo()));

                $aluno = [
                    'id' => $aluno->id,
                    'codpes' => $aluno->codpes,
                    'name' => $aluno->name,
                    'declinou' => $declinios->contains($aluno->id) ? 'sim' : 'não',
                    'media' => Utils::getMedia($notas)
                ];

                $habilitacoes = $escolhas->where('user_id', $aluno['id']);

                for ($prioridade = 1; $prioridade <= 7; $prioridade++) {
                    $habilitacao = $habilitacoes->where('prioridade', $prioridade)->first();
                    $aluno['nomhab' . $prioridade] = $habilitacao ?
                        $habilitacao->hab->nomhab . ' - ' . $habilitacao->hab->perhab : '-';
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
