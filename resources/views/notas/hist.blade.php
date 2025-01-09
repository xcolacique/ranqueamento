@extends('laravel-usp-theme::master')

@section('content')

<p><b>Nº USP: </b>{{$codpes}}</p>
<p><b>Nome: </b>{{$nome}}</p>


<p><b>Média para ranqueamento:</b> {{ number_format($media_ponderada, 2, ',') }}</p>

<div class="row">
    <table class="table table-striped">
    <tbody>
        <tr>
            <th>Código</th>
            <th>Disciplina</th>
            <th>Créditos Aula</th>
            <th>Créditos Trabalho</th>
            <th>Tipo</th>
            <th>Nota</th>
        </tr>
        @foreach($disciplinas as $disciplina)
            <tr>
                <td>{{ $disciplina['coddis'] }}</td>
                <td>{{ $disciplina['nomdis'] }}</td>
                <td>{{ $disciplina['creaul'] }}</td>
                <td>{{ $disciplina['cretrb'] }}</td>
                <td>
                    {{ $disciplina['rstfim']=='D'? 'Dispensa':'Cursada' }}
                </td>
                <td>{{ number_format($disciplina['nota'], 2, ',') }}</td>
            </tr>
        @endforeach
    </tbody>
    </table>
</div>
@endsection
