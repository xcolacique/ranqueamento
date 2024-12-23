
@extends('laravel-usp-theme::master')

@section('content')

@foreach($habs as $hab)
    <hr>
    <p style="text-align: center; font-weight: bold;">{{ $hab->nomhab }} - {{ $hab->perhab }}</p>

    <table class="table">
    <thead>
        <tr>
        <th scope="col">Classificação</th>
        <th scope="col">Média</th>
        <th scope="col">Número USP</th>
        <th scope="col">Nome</th>
        <th scope="col">Opção</th>
        </tr>
    </thead>
    <tbody>
        @php $row_number = 1; @endphp
        @foreach($scores->where('hab_id_eleita', $hab->id)->sortBy('prioridade_eleita') as $score)
        <tr>
            <td>{{ $row_number }}</td>
            <td>{{ number_format($score->nota, 2, ',') }}</td>
            <td>{{ $score->user->codpes }}</td>
            <td>{{ $score->user->name }}</td>
            <td>{{ $score->prioridade_eleita }}</td>
        </tr>
        @php $row_number = $row_number+1; @endphp
        @endforeach
    </tbody>
    </table>
@endforeach


<hr>
    <p style="text-align: center; font-weight: bold;">Estudantes que não foram selecionados em nenhuma habilitação</p>

    <table class="table">
    <thead>
        <tr>
        <th scope="col">Média</th>
        <th scope="col">Número USP</th>
        <th scope="col">Nome</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scores->where('hab_id_eleita', null)->sortByDesc('nota') as $score)
        <tr>
            <td>{{ number_format($score->nota, 2, ',') }}</td>
            <td>{{ $score->user->codpes }}</td>
            <td>{{ $score->user->name }}</td>
        </tr>
        @endforeach
    </tbody>
    </table>

@endsection
