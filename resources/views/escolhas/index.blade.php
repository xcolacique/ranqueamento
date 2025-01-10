@extends('laravel-usp-theme::master')

@section('content')

<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">E-mail</th>
      <th scope="col">Número USP</th>
      <th>Notas Ranqueamento</th>
      <th>Notas Re-ranqueamento</th>
      <th scope="col">Declinou do português?</th>
      <th scope="col">Média</th>
      <th scope="col">Classificação</th>
      <th scope="col">Opção eleita</th>
      <th scope="col">Posição</th>
      @for($prioridade = 1; $prioridade <= $ranqueamento->max; $prioridade++)
        <th> Opção {{ $prioridade }}</th>
      @endfor
    </tr>
  </thead>
  <tbody>
  @can("admin")
  <a class="btn btn-success" href="/excel/{{ $ranqueamento->id }}" style="margin-bottom:5px;">Exportar Para Excel</a>
  @endcan
    @foreach($grouped as $group)
        <tr>
          <td>{{ $group['name'] }}</td>
          <td>{{ $group['email'] }}</td>
          <td>{{ $group['codpes'] }}</td>
          <td><a href="notas/{{ $group['codpes'] }}">Notas 1</a></td>
          <td><a href="hist/{{ $group['codpes'] }}">Notas 2</a></td>
          <td>{{ $group['declinou'] }}</td>
          <td>{{ number_format($group['media'], 2, ',') }}</td>
          <td>{{ $group['classificacao'] }}</td>
          <td>{{ $group['prioridade_classificacao'] }}</td>
          <td>{{ $group['posicao'] }}</td>

          @for($prioridade = 1; $prioridade <= $ranqueamento->max; $prioridade++)
            @php $key = 'nomhab' . $prioridade ; @endphp
            <td>{{ $group[$key] }}</td>
          @endfor
          
        </tr>
    @endforeach

  </tbody>
</table





@endsection
