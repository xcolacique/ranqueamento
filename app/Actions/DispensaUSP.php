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

        $query = "SELECT codpgm, coddis, codtur
                  FROM APROVEITINTGR
                  where codpes = $codpes and codrqm IN ($requerimentos)";
        $aproveitamentos = DB::fetchAll($query);

        $resultados = [];
        foreach($aproveitamentos as $aproveitamento) {
            $resultados[] = NotaUSP::handle($codpes, $aproveitamento['codpgm'], $aproveitamento['codtur'], $aproveitamento['coddis']);
        }

        return collect($resultados);

    }
}
