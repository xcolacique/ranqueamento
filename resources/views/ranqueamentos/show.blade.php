@extends('laravel-usp-theme::master')

@section('content')

<a href="/ranqueamentos/{{ $ranqueamento->id }}/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>

<br><br>
<b>Ano:</b> {{ $ranqueamento->ano }} <br>
<b>Tipo: </b> {{ $ranqueamento->tipo }}<br>
<b>Status</b>: @if($ranqueamento->status == 1) Ativado  @else Desativado @endif<br>

<table class="table">
    <thead>
        <tr>
        <th scope="col" class="w-25">Vagas</th>
        <th scope="col" class="w-25">Permitir ambos períodos?</th>
        <th scope="col" class="w-50">Habilitação</th>
        </tr>
    </thead>
    <tbody>
        @foreach($habs as $hab)
        <tr>
            <td>{{ $hab->vagas }}</td>
            <td>{{ $hab->permite_ambos_periodos ? "sim" : "não" }}</td>
            <td>{{ $hab->codhab }} {{ $hab->nomhab }} {{ $hab->perhab }}</td>
        </tr>
        @endforeach
    </tbody>
</table>


@endsection


