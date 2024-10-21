@extends('laravel-usp-theme::master')

@section('content')

<table class="table">
  <thead>
    <tr>
      <th scope="col">Número USP</th>
      <th scope="col">Nome</th>
      <th scope="col">Data de ingresso</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ciclo_basico as $discente)
    <tr>
      <td>{{ $discente['codpes'] }}</td>
      <td>{{ $discente['nompes'] }}</td>
      <td>{{ $discente['dtainivin'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

@endsection