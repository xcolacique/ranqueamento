@extends('laravel-usp-theme::master')

@section('content')

<center><b>Quantidade de elegíveis: {{ count($ciclo_basico_elegiveis) }}</b></center>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Número USP</th>
      <th scope="col">Nome</th>
      <th scope="col">Período</th>
      <th scope="col">Data de ingresso</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ciclo_basico_elegiveis as $discente)
    <tr>
      <td>{{ $discente['codpes'] }}</td>
      <td>{{ $discente['nompes'] }}</td>
      <td>{{ $discente['nomecodhab'] }}</td>
      <td>{{ $discente['dtainivin'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<center><b>Quantidade de não elegíveis: {{ count($ciclo_basico_nao_elegiveis) }}</b></center>
<table class="table">
  <thead>
    <tr>
      <th scope="col">Número USP</th>
      <th scope="col">Nome</th>
      <th scope="col">Período</th>
      <th scope="col">Data de ingresso</th>
    </tr>
  </thead>
  <tbody>
    @foreach($ciclo_basico_nao_elegiveis as $discente)
    <tr>
      <td>{{ $discente['codpes'] }}</td>
      <td>{{ $discente['nompes'] }}</td>
      <td>{{ $discente['nomecodhab'] }}</td>
      <td>{{ $discente['dtainivin'] }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

@endsection