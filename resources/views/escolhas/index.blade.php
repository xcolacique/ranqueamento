@extends('laravel-usp-theme::master')

@section('content')

<h1>Em construção</h1>

@foreach($grouped as $group)
    @foreach($group as $escolha)
        {{ $escolha }}
    @endforeach
@endforeach


@endsection