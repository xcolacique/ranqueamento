<?php

namespace App\Services;

use Uspdev\Replicado\DB;
use App\Models\Declinio;
use App\Models\Ranqueamento;
use App\Models\Escolha;
use App\Models\Hab;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class Utils
{
    // AND YEAR(V.dtainivin) = 2024
    public static function ciclo_basico_elegiveis(int $ano){
        // codhab=102 : Ciclo Básico matutino
        // codhab=104 : Ciclo Básico noturno
        // Elegiveis: estão matriculados no semestre 2 no ano, ingressaram no ano e são ciclo básico
        // Conversão da data no sybase:
        // https://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.infocenter.dc38151.1520/html/iqrefbb/Convert.htm
        $anosem = "{$ano}2";
        $query = "SELECT V.codpes, V.nompes, S.perhab, CONVERT( CHAR( 20 ), V.dtainivin, 103 ) AS dtainivin
        FROM VINCULOPESSOAUSP V
        INNER JOIN SITALUNOATIVOGR S ON (V.codpes = S.codpes) AND (V.codclg = S.codclg)
        WHERE V.tipvin = 'ALUNOGR'
            AND (V.codclg = 8)
            AND ((V.codhab=102 OR V.codhab=104) AND (S.codhab=102 OR S.codhab=104))
            AND (V.codcurgrd = 8051)
            AND YEAR(dtainivin) = {$ano}
            AND S.anosem = {$anosem}
            AND (S.staalu = 'M' OR S.staalu = 'A' OR S.staalu = 'R')
            ORDER BY V.nompes ASC
        ";
        return DB::fetchAll($query);
    }

    // AND YEAR(V.dtainivin) = 2024
    public static function ciclo_basico_nao_elegiveis(int $ano){
        // codhab=102 : Ciclo Básico matutino
        // codhab=104 : Ciclo Básico noturno
        // Elegiveis: estão matriculados no semestre 2 no ano, ingressaram no ano e são ciclo básico
        // Conversão da data no sybase:
        // https://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.infocenter.dc38151.1520/html/iqrefbb/Convert.htm
        $anosem = "{$ano}2";
        $query = "SELECT V.codpes, V.nompes, S.perhab, S.staalu, CONVERT( CHAR( 20 ), V.dtainivin, 103 ) AS dtainivin
        FROM VINCULOPESSOAUSP V
        INNER JOIN SITALUNOATIVOGR S ON (V.codpes = S.codpes) AND (V.codclg = S.codclg)
        WHERE V.tipvin = 'ALUNOGR'
            AND (V.codclg = 8)
            AND ((V.codhab=102 OR V.codhab=104) AND (S.codhab=102 OR S.codhab=104))
            AND (V.codcurgrd = 8051)
            AND S.anosem = {$anosem}
            AND
                (YEAR(dtainivin) <> {$ano} OR (S.staalu <> 'M' AND S.staalu <> 'A' AND S.staalu <> 'R'))
            ORDER BY V.nompes ASC
        ";
        return DB::fetchAll($query);
    }

    public static function ciclo_basico_check(int $codpes, int $ano){
        $anosem = "{$ano}2";

        $query = "SELECT COUNT(*)
        FROM VINCULOPESSOAUSP V
        INNER JOIN SITALUNOATIVOGR S ON (V.codpes = S.codpes) AND (V.codclg = S.codclg)
        WHERE V.tipvin = 'ALUNOGR'
            AND (V.codclg = 8)
            AND ((V.codhab=102 OR V.codhab=104) AND (S.codhab=102 OR S.codhab=104))
            AND (V.codcurgrd = 8051)
            AND YEAR(dtainivin) = {$ano}
            AND S.anosem = {$anosem}
            AND (S.staalu = 'M' OR S.staalu = 'A' OR S.staalu = 'R')
            AND V.codpes = $codpes
        ";
        $record = DB::fetch($query);
        return (bool)$record['computed'];
    }

    public static function lista_habs(){
        $query = "SELECT H.codhab, H.nomhab, H.perhab
            FROM CURSOGR C, HABILITACAOGR H
            WHERE C.codclg = 8
            AND C.codcur = 8051
            AND H.nomhab NOT LIKE '%Portugu%'
            AND H.tiphab = 'I'
            AND C.codcur = H.codcur
            AND ( (H.dtaatvhab IS NOT NULL) AND (H.dtadtvhab IS NULL) )
            ORDER BY H.nomhab ASC
        ";
        return DB::fetchAll($query);
    }

    public static function periodo($codpes = null){
        if(is_null($codpes)) $codpes = auth()->user()->codpes;

        $query = "SELECT V.codhab
        FROM VINCULOPESSOAUSP V
        WHERE V.tipvin = 'ALUNOGR'
            AND (V.codclg = 8)
            AND (V.codhab=102 OR V.codhab=104)
            AND (V.codcurgrd = 8051)
            AND V.codpes = $codpes
        ";
        $record = DB::fetch($query);
        if(!$record) return '';
        if($record['codhab'] == 102) return 'matutino';
        if($record['codhab'] == 104) return 'noturno';
    }

    public static function get_hab($codhab){
        $query = "SELECT H.codhab, H.nomhab, H.perhab
            FROM CURSOGR C, HABILITACAOGR H
            WHERE C.codclg = 8
            AND C.codcur = 8051
            AND H.tiphab = 'I'
            AND C.codcur = H.codcur
            AND H.codhab = $codhab
            AND ( (H.dtaatvhab IS NOT NULL) AND (H.dtadtvhab IS NULL) )
        ";
        return DB::fetch($query);
    }

    public static function limpa_string_de_codpes($string){
        $array = explode(',',$string);
        $array = array_map('trim', $array);
        $array = array_unique($array);
        sort($array);
        return implode(',',$array);
    }

    public static function escolha($prioridade){
        $ranqueamento = Ranqueamento::where('status',1)->first();

        $hab_id = Escolha::select('hab_id')
                            ->where('ranqueamento_id',$ranqueamento->id)
                            ->where('user_id',auth()->user()->id)
                            ->where('prioridade',$prioridade)
                            ->first();

        if(!$hab_id) return 'Não definido';
        $hab = Hab::find($hab_id->hab_id);
        return $hab->nomhab . ' - ' . $hab->perhab;
    }

    public static function get_codpgm(int $codpes) {
        $query = "SELECT codpgm
                    FROM PROGRAMAGR
                    WHERE codpes = $codpes AND stapgm = 'A'";
        $result = DB::fetch($query);
        if($result) return $result['codpgm'];
        return null;
    }

    public static function getNotas(int $codpes, array $disciplinas) {
        $disciplinas = implode(',', $disciplinas);

        $query = "SELECT coddis, rstfim, notfim, notfim2, codpgm
            FROM HISTESCOLARGR
            WHERE codpgm = (
                SELECT codpgm
                FROM PROGRAMAGR
                WHERE codpes = $codpes AND stapgm = 'A'
            ) AND codpes = $codpes AND stamtr = 'M' AND coddis IN($disciplinas)";
        $resultados = DB::fetchAll($query);

        [$disciplinas, $aproveitamentos] = collect($resultados)->partition(function($disciplina) {
            return $disciplina['rstfim'] <> 'D';
        });

        $notas = $disciplinas->map(function($disciplina) {
            $nota = $disciplina['notfim'] ? $disciplina['notfim'] : 0;
            // vale a maior nota entre notfim e notfim2
            if($disciplina['notfim2'] &&  $disciplina['notfim2'] > $nota){
                $nota = $disciplina['notfim2'];
            }
            return [
                'coddis' => $disciplina['coddis'],
                'nota'   => $nota
            ];

        });

        $codpgm = $aproveitamentos->select('codpgm')->first();

        $disciplinasAproveitamentos = $aproveitamentos->map(function($disciplina) {
            return "'" . $disciplina['coddis'] . "'";
        });

        $notasEquivalencia = $disciplinasAproveitamentos->isEmpty() ? collect([]) :
            self::getAproveitamentos($codpes, $codpgm['codpgm'], $disciplinasAproveitamentos->toArray());

        return $notas->merge($notasEquivalencia);

    }

    private static function getAproveitamentos(int $codpes, int $codpgm, array $disciplinas) {
        $disciplinas = implode(',', $disciplinas);

        $query = "SELECT R.coddis, count(R.coddis) as qtdedisc, sum(H.notdisexr) as nota
            FROM REQUERHISTESC R
            INNER JOIN APROVEITEXTGR A ON (R.codrqm = A.codrqm)
            INNER JOIN HISTESCOLAREXTGR H ON
                (A.coduspdisexr = H.coduspdisexr AND R.codpes = H.codpes)
            INNER JOIN DISCIPEXTGR D ON (H.coduspdisexr = D.coduspdisexr)
            INNER JOIN ORGANIZACAO O ON (D.codorg = O.codorg)
            WHERE R.codpes = $codpes AND R.codpgm = $codpgm AND R.coddis IN ($disciplinas)
            GROUP BY R.coddis";

        $aproveitamentos = DB::fetchAll($query);

        return collect($aproveitamentos)->map(function ($aproveitamento) {
            return [
                'coddis' => $aproveitamento['coddis'],
                'nota'   => $aproveitamento['nota'] / $aproveitamento['qtdedisc']
            ];
        });

    }

    public static function getMedia($notas) {
        $disciplinas = Escolha::disciplinas();

        [$primeiro, $segundo] = $notas->partition(function($nota) use($disciplinas) {
            return in_array("'" . $nota['coddis'] . "'", $disciplinas);
        });

        return ((($primeiro->sum('nota') / 4) + ($segundo->sum('nota') / 2)) / 3);
    }

}
