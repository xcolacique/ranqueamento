<?php

namespace App\Service;

use Uspdev\Replicado\DB;

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

    public static function get_hab($codhab){
        $query = "SELECT H.codhab, H.nomhab, H.perhab
            FROM CURSOGR C, HABILITACAOGR H
            WHERE C.codclg = 8
            AND C.codcur = 8051
            AND H.nomhab NOT LIKE '%Portugu%'
            AND H.tiphab = 'I'
            AND C.codcur = H.codcur
            AND H.codhab = $codhab
            AND ( (H.dtaatvhab IS NOT NULL) AND (H.dtadtvhab IS NULL) )
            ORDER BY H.nomhab ASC
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
}