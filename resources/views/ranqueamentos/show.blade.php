@extends('laravel-usp-theme::master')

@section('content')

<a href="/ranqueamentos/{{ $ranqueamento->id }}/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>

<br><br>
<b>Ano:</b> {{ $ranqueamento->ano }} <br>
<b>Tipo: </b> {{ $ranqueamento->tipo }}<br>
<b>Status</b>: @if($ranqueamento->status == 1) Ativado  @else Desativado @endif<br>

@endsection


