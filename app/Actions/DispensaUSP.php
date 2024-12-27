<?php

namespace App\Actions;

use Uspdev\Replicado\DB;
use App\Actions\NotaUSP;

class DispensaUSP
{
    /**
     * Retorna as disciplinas e as notas do Aproveitamento
     * Interno (Dispensa USP)
     */
    public static function handle(int $codpes, $rqmInternos)
    {
        $requerimentos = $rqmInternos->map(function($requerimento) {
            return $requerimento['codrqm'];
        })->implode(', ');

        $query = "SELECT codpgm, coddis, codtur, codrqm
                  FROM APROVEITINTGR
                  where codpes = $codpes and codrqm IN ($requerimentos)";
        $aproveitamentos = DB::fetchAll($query);

        $resultados = [];
        foreach($aproveitamentos as $aproveitamento) {
            $coddisAtual = $rqmInternos->where('codrqm', $aproveitamento['codrqm'])->pluck('coddis');
            $resultados[] = NotaUSP::handle($codpes, $coddisAtual[0], $aproveitamento['codpgm'], $aproveitamento['codtur'], $aproveitamento['coddis']);
        }

        $disciplinas = collect($resultados)->groupBy('coddis')->map(function($item) {
            return [
                'coddis' => $item[0]['coddis'],
                'nota'   => (count($item) > 1) ? $item->avg('nota') : $item[0]['nota']
            ];
        });

        return $disciplinas;

    }
}
