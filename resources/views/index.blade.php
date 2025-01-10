@extends('laravel-usp-theme::master')

@section('content')
  @can('elegivel')
    <div class="card">
      <div class="card-header">
        Meu ranqueamento
      </div>
      <div class="card-body">
        <h5 class="card-title"></h5>
        <p class="card-text">
          @include('escolhas.partials.info')
          <br>
          @include('escolhas.partials.declinio')
          <br>
            <a href="{{route('escolhas_form')}}" class="btn btn-primary">Escolher habilitação(ões) para ranqueamento</a>
            <br><br>
            @include('escolhas.partials.show')
        </p>
        <br>
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
  @endcan('elegivel')
@endsection