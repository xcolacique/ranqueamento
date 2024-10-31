@extends('laravel-usp-theme::master')

@section('content')

<a href="{{ route('ranqueamentos.create') }}" class="btn btn-success">
    Adicionar novo ranqueamento
</a>
<br><br>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Ano</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>Inscrições</th>
                <th colspan="2">Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ranqueamentos as $ranqueamento)
            <tr>
                <td><a href="/ranqueamentos/{{ $ranqueamento->id }}">{{ $ranqueamento->ano }}</a></td>
                <td>{{ $ranqueamento->tipo }}</td>
                <td>@if($ranqueamento->status == 1) Ativado
                    @else Desativado
                    @endif
                </td>
                <td><a href="/escolhas/{{ $ranqueamento->id }}" class="btn btn-info">Inscrições</a></td>
                <td>
                    <a href="/ranqueamentos/{{ $ranqueamento->id }}/edit" class="btn btn-warning"><i class="fas fa-pencil-alt"></i></a>
                </td>
                <td>
                    <form action="/ranqueamentos/{{ $ranqueamento->id }}" method="post">
                        @csrf
                        @method('delete')
                        <button class="delete-item btn btn-danger" type="submit"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop
