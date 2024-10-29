@extends('laravel-usp-theme::master')

@section('content')
  @can('ciclo_basico')
    <div class="card">
      <div class="card-header">
        Meu ranqueamento
      </div>
      <div class="card-body">
        <h5 class="card-title"></h5>
        <p class="card-text">
          <b>Número USP:</b> {{ auth()->user()->codpes }}<br>
          <b>Nome:</b> {{ auth()->user()->name }}<br>
          <b>Email:</b> {{ auth()->user()->email }}<br>
          <b>Período:</b> {{ \App\Service\Utils::periodo(auth()->user()->codpes) }}<br>

        </p>
        <a href="#" class="btn btn-primary">Iniciar ou continuar Ranqueamento</a>
      </div>
    </div>
  @else
    @auth
      <div class="card">
        <div class="card-body">
          <p class="card-text">Você não está no ciclo básico e portanto não pode participar do ranqueamento</p>
        </div>
      </div>
    @else
      <div class="card">
        <div class="card-body">
          <p class="card-text">Sistema para ranqueamento de habilitações no curso de Letras</p>
          <a href="/login" class="btn btn-primary">Acessar sistema</a>
        </div>
      </div>
    @endauth
  @endcan('ciclo_basico')
@endsection