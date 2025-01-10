<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use App\Services\Utils;
use App\Models\Escolha;
use App\Models\User;
use Uspdev\Replicado\Pessoa;

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
            $notas = Utils::getNotas($codpes, array_column($disciplinas, 'coddis'),true);
        }

        $disciplinas = Utils::combina_disciplinas_notas($disciplinas, $notas);
        $media_ponderada = Utils::obterMediaPonderada($disciplinas);

        return view('notas.hist', [
            'codpes' => $codpes,
            'nome' => Pessoa::retornarNome($codpes),
            'disciplinas' => $disciplinas,
            'media_ponderada' => $media_ponderada,
        ]);
    }
}
