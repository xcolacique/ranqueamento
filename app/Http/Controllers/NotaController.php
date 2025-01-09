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
            'media' => $media,
        ]);
    }

    public function hist($codpes) {
        Gate::authorize('admin');

        $disciplinas = Utils::disciplinas_aprovadas_ou_dispensadas($codpes);

        $notas = [];
        if($disciplinas) {
            $notas = Utils::getNotas($codpes, array_column($disciplinas, 'coddis'));
        }
    }
}
