<?php

namespace App\Actions;

use Uspdev\Replicado\DB;

class DispensaExterna
{
    /**
     * Retorna notas das disciplinas do Aproveitamento
     * Externo (Dispensa Externa)
     */
    public static function handle(int $codpes, int $codpgm, $rqmExternos)
    {
        $disciplinas = $rqmExternos->map(function($disciplina) {
                return "'" . $disciplina['coddis'] . "'";
            })->implode(', ');

        $query = "SELECT R.coddis, count(R.coddis) as qtdedisc, sum(H.notdisexr) as nota
            FROM REQUERHISTESC R
            INNER JOIN REQUERIMENTOGR R2 ON (R.codrqm = R2.codrqm)
            INNER JOIN APROVEITEXTGR A ON (R.codrqm = A.codrqm)
            INNER JOIN HISTESCOLAREXTGR H ON
                (A.coduspdisexr = H.coduspdisexr AND R.codpes = H.codpes)
            WHERE R.codpes = $codpes AND R.codpgm = $codpgm and R2.rstfim = 'D' and R2.starqm = 'C' AND R.coddis IN ($disciplinas)
            GROUP BY R.coddis";

        $aproveitamentos = DB::fetchAll($query);

        return collect($aproveitamentos)->map(function ($aproveitamento) {
            if(!is_numeric($aproveitamento['nota'])) $aproveitamento['nota'] = 0;
            return [
                'coddis' => $aproveitamento['coddis'],
                'nota'   => $aproveitamento['nota'] / $aproveitamento['qtdedisc']
            ];
        });

    }
}
