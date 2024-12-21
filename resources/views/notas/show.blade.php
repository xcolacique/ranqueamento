@extends('laravel-usp-theme::master')

@section('content')

<p><b>Nº USP: </b>{{$user->codpes}}</p>
<p><b>Nome: </b>{{$user->name}}</p>
<!-- media query -->
<link rel="stylesheet" href="{{ asset('css/style.css') }}">

<div class="row" style="margin:8px;">
    <table class="table table-striped" style="width:25%; margin-right:10px;">
    <tbody>
        <tr>
            <th>Disciplinas</th>
            <th>Nota</th>
        </tr>
        @foreach($notas as $nota)
            <tr>
                <th scope="row">{{ $nota['coddis'] }}</th>
                <td>{{ number_format($nota['nota'], 2, ',') }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Média para ranqueamento:</th>
            <td>{{ number_format($media, 2, ',') }}</td>
        </tr>
    </tbody>
    </table>
</div>
@endsection
