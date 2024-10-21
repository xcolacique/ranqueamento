@extends('laravel-usp-theme::master')

@section('content')
  @can('ciclo_basico')
    <div class="card">
      <div class="card-header">
        Meu ranqueamento
      </div>
      <div class="card-body">
        <h5 class="card-title">Meu ranqueamento</h5>
        <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
        <a href="#" class="btn btn-primary">Go somewhere</a>
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