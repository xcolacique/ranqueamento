<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Ranqueamento;
use App\Models\Score;
use App\Services\Utils;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('elegivel', function (User $user) {
            $ranqueamento = Ranqueamento::where('status',1)->first();

            // não há ranqueamento ativo
            if(!$ranqueamento) return false;

            if(in_array($user->codpes,explode(',',$ranqueamento->permitidos))) return true;

            if($ranqueamento && $ranqueamento->tipo=='ingressantes') {
                return Utils::ciclo_basico_check($user->codpes, $ranqueamento->ano);
            }
            if($ranqueamento && $ranqueamento->tipo=='reranqueamento') {
                // alunos que participaram do último ranqueamento e ficaram na primeira posição
                // não podem participar do re-ranqueamento
                $ultimo_ranqueamento = Ranqueamento::where('ano',$ranqueamento->ano-1)
                                                    ->where('tipo','ingressantes')
                                                    ->first();
                if($ultimo_ranqueamento){
                    $score = Score::where('user_id',$user->id)
                                   ->where('ranqueamento_id',$ultimo_ranqueamento->id)
                                   ->where('posicao',1)
                                   ->first();
                    if($score) return false;
                }
                return Utils::reranqueamento_check($user->codpes);
            }

            return false;
        });
    }
}
