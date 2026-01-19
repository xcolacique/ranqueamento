@extends('laravel-usp-theme::master')

@section('content')
@can('elegivel')
  <div class="card">
    <div class="card-header">Meu ranqueamento</div>
    <div class="card-body">
      @include('escolhas.partials.info')
      <br>
      <a href="{{ route('escolhas_form') }}" class="btn btn-primary">
        Escolher habilitação(ões)
      </a>
      <br><br>
      @include('escolhas.partials.show')
    </div>
  </div>
@endcan


@cannot('elegivel')
  @can('ver-resultado')
    <div class="card">
      <div class="card-header">Resultado do último ranqueamento</div>
      <div class="card-body">
        @include('escolhas.partials.resultado')
      </div>
    </div>
  @endcan
@endcannot


@auth
  @cannot('elegivel')
    @cannot('ver-resultado')
      <div class="card">
        <div class="card-body">
          <p>
            Você não está apto(a) a participar do ranqueamento atual ou não há ranqueamento em aberto.
          </p>
        </div>
      </div>
    @endcannot
  @endcannot
@endauth

@endsection