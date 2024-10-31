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
          <b>Período:</b> {{ \App\Service\Utils::periodo() }}<br>
          @if(\App\Service\Utils::declinou())
            <form class="form-inline" method="POST" action="{{ route('declinar') }}">
              @csrf
              <b>Declinou do português?</b>&nbsp;sim&nbsp;
              <button type="submit"  role="form"class="btn btn-warning"  name="declinar" value=0 
                onclick="return confirm('Tem certeza que deseja cancelar a declinação do português?');"> Cancelar declinação 
              </button>
            </form>
          @else 
            <form class="form-inline" method="POST" action="{{ route('declinar') }}">
              @csrf
              <b>Declinou do português?</b>&nbsp;não&nbsp; 
              <button type="submit" class="btn btn-warning" name="declinar" value=1 
                onclick="return confirm('Tem certeza que deseja declinar do português?');"> Quero declinar 
              </button>
            </form>
          @endif
        </p>
        <br>
        <a href="#" class="btn btn-primary">Iniciar ou continuar Ranqueamento</a>
      </div>
    </div>
  @else
    @auth
      <div class="card">
        <div class="card-body">
          <p class="card-text">Você não está apto(a) a participar do ranqueamento atual ou não há ranqueamento em aberto</p>
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