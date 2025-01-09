<?php

namespace App\Services;

use Uspdev\Replicado\DB;
use App\Models\Declinio;
use App\Models\Ranqueamento;
use App\Models\Escolha;
use App\Models\Hab;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use App\Actions\DispensaExterna;
use App\Actions\DispensaUSP;
use App\Actions\MaiorNota;

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

    public static function reranqueamento_check(int $codpes){
        // primeiro verificamos se é aluno(a) de letras
        $query = "SELECT V.codpes FROM VINCULOPESSOAUSP V
                    WHERE V.tipvin = 'ALUNOGR'
                        AND (V.codpes= {$codpes})
                        AND (V.codclg = 8)
                        AND (V.codcurgrd = 8051)";
        $record = DB::fetch($query);
        if(!$record) return false;

        // podem participar do reranqueamento os alunos:
        // 1. Cursaram no máximo oito semestres retroativos
        // 2. Não estão com o curso trancado 
        $query = "SELECT * FROM SITALUNOATIVOGR 
                    WHERE codpes = {$codpes}
                    AND staalu='M'
                    AND codclg=8
                    AND codcur=8051
                    AND codpgm = (
                                    SELECT codpgm
                                    FROM PROGRAMAGR
                                    WHERE codpes = {$codpes} -- Permitir trancados? AND stapgm = 'A'
                                )
                   ";
        $records = DB::fetchAll($query);
        if(count($records)<=8) return true;
        return false;
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

        $query = "SELECT H.perhab FROM VINCULOPESSOAUSP V
                    INNER JOIN HABILITACAOGR H ON V.codhab=H.codhab
                    WHERE V.tipvin = 'ALUNOGR'
                        AND (V.codclg = 8)
                        AND (V.codcurgrd = 8051 AND H.codcur = 8051)
                        AND V.codpes = {$codpes}
                        AND ((H.dtaatvhab IS NOT NULL) AND (H.dtadtvhab IS NULL))";

        $record = DB::fetch($query);
        if($record) return $record['perhab'];
        return '';
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
        if(empty($disciplinas)) return [];

        $disciplinas = collect($disciplinas)->map(function($disciplina) {
                return "'" . $disciplina . "'";
            })->implode(',', $disciplinas);

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
            return [
                'coddis' => $disciplina['coddis'],
                'nota'   => MaiorNota::handle($disciplina)
            ];

        });

        $codpgm = $aproveitamentos->select('codpgm')->first();

        $notasEquivalencia = $aproveitamentos->isEmpty() ? collect([]) :
            self::getAproveitamentos($codpes, $codpgm['codpgm']);

        return $notas->merge($notasEquivalencia);

    }

    private static function getAproveitamentos(int $codpes, int $codpgm) {

        $requerimento = "SELECT R2.coddis, R.tiprqm, R.codrqm FROM REQUERIMENTOGR R
                         INNER JOIN REQUERHISTESC R2 ON (R.codrqm = R2.codrqm)
                         WHERE R.codpes = $codpes AND R.codpgm = $codpgm AND R.rstfim = 'D'
                         AND R.starqm = 'C' AND R.tiprqm IN ('Dispensa Externa','Dispensa USP')";

        $requerimentos = DB::fetchAll($requerimento);

        [$rqmExternos, $rqmInternos] = collect($requerimentos)->partition(function($requerimento) {
            return $requerimento['tiprqm'] === 'Dispensa Externa';
        });

        $externo = $rqmExternos->isEmpty() ? collect([]) : DispensaExterna::handle($codpes, $codpgm, $rqmExternos);
        $interno = $rqmInternos->isEmpty() ? collect([]) : DispensaUSP::handle($codpes, $rqmInternos);

        return $externo->merge($interno);
    }

    public static function getMedia($notas) {
        $disciplinas = Escolha::disciplinas();

        [$primeiro, $segundo] = $notas->partition(function($nota) use($disciplinas) {
            return in_array($nota['coddis'], $disciplinas);
        });

        return ((($primeiro->sum('nota') / 4) + ($segundo->sum('nota') / 2)) / 3);
    }

    public static function declinou($user_id = null, $ranqueamento = null){
        if(is_null($user_id)) $user_id = auth()->user()->id;
        if(is_null($ranqueamento))  $ranqueamento = Ranqueamento::where('status',1)->first();
       
        $declinio = Declinio::where('ranqueamento_id',$ranqueamento->id)
                            ->where('user_id',$user_id)->first();

        if($declinio) return true;
        return false;
    }

    public static function disciplinas_aprovadas_ou_dispensadas($codpes){
        $query = "SELECT D.coddis, D.nomdis, D.creaul, D.cretrb
                    FROM HISTESCOLARGR H
                    INNER JOIN DISCIPLINAGR D ON H.coddis = D.coddis AND H.verdis = D.verdis
                    WHERE H.codpes = {$codpes}
                        AND (H.rstfim='A' OR H.rstfim='D')
                        AND codpgm = (
                                    SELECT codpgm
                                    FROM PROGRAMAGR
                                    WHERE codpes = {$codpes} AND stapgm = 'A'
                                )";
        return DB::fetchAll($query);

    }
}
