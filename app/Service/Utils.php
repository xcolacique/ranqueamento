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
            AND YEAR(dtainivin) <> {$ano}
            AND S.anosem = {$anosem}
            ORDER BY V.nompes ASC
        ";
        return DB::fetchAll($query);    
    }

    public static function ciclo_basico_check(int $codpes){
        $query = "SELECT COUNT(*)
        FROM VINCULOPESSOAUSP V
        WHERE V.tipvin = 'ALUNOGR'
            AND V.codclg = 8
            AND V.sitatl = 'A'
            AND (V.codhab=102 OR V.codhab=104)
            AND V.codcurgrd = 8051
            AND V.codpes = $codpes
        ";
        $record = DB::fetch($query);
        return (bool)$record['computed'];
    }
}