@extends('laravel-usp-theme::master')

@section('content')
    
    <div class="card">
        <div class="card-header">Cadastrar Ranqueamento</div>
            <div class="card-body">
                <form method="post" action="/ranqueamentos/{{$ranqueamento->id}}">
                    @csrf
                    @method('patch')
                    <div class="row">

                        <div class="col-sm form-group">
                            <label for="nome">Ano</label>
                            <input type="text" class="form-control" name="ano" value="{{ old('ano', $ranqueamento->ano) }}" required >
                        </div>

                        <div class="col-sm form-group">
                            <label for="nome">Tipo</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" id="tipo_ingressantes" value="ingressantes" @if($ranqueamento->tipo == 'ingressantes') checked @endif>
                                <label class="form-check-label" for="tipo_ingressantes">
                                    Ingressantes
                                </label>
                                </div>
                                <div class="form-check">
                                <input class="form-check-input" type="radio" name="tipo" value="reranqueamento" id="tipo_reranqueamento" @if($ranqueamento->tipo == 'reranqueamento') checked @endif>
                                <label class="form-check-label" for="tipo_reranqueamento">
                                    Reranqueamento
                                </label>
                            </div>
                        </div>

                        <div class="col-sm form-group">
                            <div class="form-check-status">
                                <input class="form-check-status-input" type="checkbox" name="status" value="1" id="status" @if($ranqueamento->status == 1) checked @endif>
                                <label class="form-check-status-label" for="status">
                                    Marque para tornar esse ranqueamento ativo
                                </label>
                                <br><small>Ao marcar esse ranqueamento como ativo, qualquer outro ranqueamento será desativado</small>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Cadastrar">
                    </div>
                </form>
        </div>
    </div>

@endsection