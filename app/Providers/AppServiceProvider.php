<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\User;
use App\Models\Ranqueamento;
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

            if($ranqueamento && $ranqueamento->tipo=='ingressantes') {
                return
                    Utils::ciclo_basico_check($user->codpes, $ranqueamento->ano)
                    | in_array($user->codpes,explode(',',$ranqueamento->permitidos));
            }
            if($ranqueamento && $ranqueamento->tipo=='reranqueamento') {
                return true;
            }

            return false;
        });
    }
}
