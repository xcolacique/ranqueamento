<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Hab;

class EscolhaRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 0. todas opções não podem ser nulas
        //if(empty(array_filter($value))){
        //    $fail("Escolha ao menos uma opção!");
        //}

        // 1. verificar se estão preenchidos na sequência
        $hab_id_anterior = 'qualquer coisa';
        foreach($value as $prioridade=>$hab_id) {
            if(!is_null($hab_id) && is_null($hab_id_anterior)){
                $prioridade_nao_nula = (int)$prioridade-1;
                $fail("A opção {$prioridade_nao_nula} não pode ser vazia!");
            }
            $hab_id_anterior = $hab_id;
        }

        // 2. verificar se não há escolha duplicadas
        $escolhas = array_filter($value);
        $duplicados = array_unique( array_diff_assoc( $escolhas, array_unique( $escolhas )));
        if(!empty($duplicados)){
            foreach($duplicados as $duplicado){
                $hab = Hab::find($duplicado);
                $fail("A habilitação '{$hab->nomhab}' foi escolhida mais que uma vez!");
            }
        }
    }
}
