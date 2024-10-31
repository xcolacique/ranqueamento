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
                    @php 
                        // verificando se há habilitação escolhida para cada opção
                        $escolha_salva = $escolhas->where('prioridade', $prioridade)->first();
                        $hab_id_salva = 0;
                        if($escolha_salva) $hab_id_salva = $escolha_salva->hab_id;

                        $hab_id_old = 0;
                        $old = old('habs');
                        if($old) {
                            $hab_id_old = $old[$prioridade];
                        }
                    @endphp

                    <div class="form-group">
                        <label for="select{{$prioridade}}">Opção {{ $prioridade }}</label>
                        <select class="form-control" id="select{{$prioridade}}" name="habs[{{$prioridade}}]">
                            <option value="" selected=""> - Selecione  -</option>
                            @foreach($habs as $hab)
                                <option value="{{ $hab->id }}" @if($hab_id_salva==$hab->id or $hab_id_old==$hab->id) selected @endif>{{$hab->nomhab}} - {{$hab->perhab}}</option>
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