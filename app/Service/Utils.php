<?php

namespace App\Service;

use Uspdev\Replicado\DB;

class Utils
{
    // AND YEAR(V.dtainivin) = 2024
    public static function ciclo_basico(){
        // codhab=102 : Ciclo Básico matutino
        // codhab=104 : Ciclo Básico noturno
        // Conversão da data no sybase:
        // https://infocenter.sybase.com/help/index.jsp?topic=/com.sybase.infocenter.dc38151.1520/html/iqrefbb/Convert.htm
        $query = "SELECT V.codpes, V.nompes,  CONVERT( CHAR( 20 ), V.dtainivin, 103 ) AS dtainivin
        FROM VINCULOPESSOAUSP V
        WHERE V.tipvin = 'ALUNOGR'
            AND V.codclg = 8
            AND V.sitatl = 'A'
            AND (V.codhab=102 OR V.codhab=104)
            AND V.codcurgrd = 8051
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