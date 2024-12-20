@extends('laravel-usp-theme::master')

@section('content')

<table class="table table-bordered">
  <thead>
    <tr>
      <th scope="col">Nome</th>
      <th scope="col">Número USP</th>
      <th scope="col">Declinou do português?</th>
      <th scope="col">Média</th>
      @for($prioridade = 1; $prioridade <=7; $prioridade++)
        <th>
            Opção {{ $prioridade }}
        </th>
      @endfor
    </tr>
  </thead>
  <tbody>
  @can("admin")
  <a class="btn btn-success" href="/excel/{{ $ranqueamento->id }}" style="margin-bottom:5px;">Exportar Para Excel</a>
  @endcan
    @foreach($grouped as $group)
        <tr>
          <td><a href="notas/{{ $group['codpes'] }}">{{ $group['name'] }}</a></td>
          <td>{{ $group['codpes'] }}</td>
          <td>{{ $group['declinou'] }}</td>
          <td>{{ $group['media'] }}</td>
          <td>{{ $group['nomhab1'] }}</td>
          <td>{{ $group['nomhab2'] }}</td>
          <td>{{ $group['nomhab3'] }}</td>
          <td>{{ $group['nomhab4'] }}</td>
          <td>{{ $group['nomhab5'] }}</td>
          <td>{{ $group['nomhab6'] }}</td>
          <td>{{ $group['nomhab7'] }}</td>
        </tr>
    @endforeach

  </tbody>
</table





@endsection
