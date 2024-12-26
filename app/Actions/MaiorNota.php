<?php

namespace App\Actions;

class MaiorNota
{
    /**
     * Retorna a Maior Nota entre notfim e notfim2
     */
    public static function handle(array $disciplina)
    {
        $nota = $disciplina['notfim'] ? $disciplina['notfim'] : 0;
        if($disciplina['notfim2'] &&  $disciplina['notfim2'] > $nota){
            $nota = $disciplina['notfim2'];
        }

        return $nota;
    }
}
