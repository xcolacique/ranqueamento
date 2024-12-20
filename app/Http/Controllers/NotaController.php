<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\Utils;
use App\Models\Escolha;
use App\Models\User;

class NotaController extends Controller
{
    public function show($codpes) {
        Gate::authorize('admin');
        $notas = Utils::getNotas($codpes, array_merge(Escolha::disciplinas(),Escolha::disciplinas_segundo()));
        $media = Utils::getMedia($notas);

        return view('notas.show', [
            'user' => User::where('codpes',$codpes)->first(),
            'notas' => $notas,
            'notas_segundo' => $notas_segundo,
            'soma_notas' => $soma_notas,
            'soma_notas2' => $soma_notas2,
            'media_um' => $media_um,
            'media_dois' => $media_dois,
            'media_final' => $media_final
        ]);
    }
}
