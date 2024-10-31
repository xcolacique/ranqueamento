@extends('laravel-usp-theme::master')

@section('content')

@include('escolhas.partials.info')
<br>
<div class="card">
    <div class="card-header">Opções para ranqueamento</div>
        <div class="card-body">
            <form method="post" action="{{ route('escolhas_store') }}">
                @csrf

                @for($prioridade = 1; $prioridade <=7; $prioridade++)
                    <div class="form-group">
                        <label for="select{{$prioridade}}">Opção {{ $prioridade }}</label>
                        <select class="form-control" id="select{{$prioridade}}" name="habs[{{$prioridade}}]">
                            <option value="" selected=""> - Selecione  -</option>
                            @foreach($habs as $hab)
                                <option value="{{ $hab->codhab }}">{{$hab->nomhab}}</option>
                            @endforeach
                        </select>
                    </div>
                @endfor

                <div class="form-group">
                    <input type="submit" class="btn btn-primary" value="Salvar">
                </div>
            </form>
    </div>
</div>

@endsection