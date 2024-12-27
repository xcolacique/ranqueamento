<?php

namespace App\Actions;

use Uspdev\Replicado\DB;
use App\Actions\MaiorNota;

class NotaUSP
{
    /**
     * Retorna a disciplina e a maior nota do Aproveitamento USP
     */
    public static function handle(int $codpes, string $coddisAtual, int $codpgm, string $codtur, string $coddis)
    {
        $query = "SELECT coddis, notfim, notfim2
            FROM HISTESCOLARGR
            WHERE codpes = $codpes AND codpgm = $codpgm
            AND codtur = '$codtur' AND stamtr = 'M'
            AND coddis = '$coddis'";

        $disciplina =  DB::fetch($query);

        return [
            'coddis' => $coddisAtual,
            'nota'   => MaiorNota::handle($disciplina)
        ];
    }
}
