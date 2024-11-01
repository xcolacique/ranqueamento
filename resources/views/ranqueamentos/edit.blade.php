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
                        <label for="permitidos">Autorizar discentes não aptos a participarem desse ranqueamento</label>
                        <textarea class="form-control" id="permitidos" name="permitidos" rows="3">{{ old('permitidos',$ranqueamento->permitidos) }}</textarea>
                        <small>Digite os números USP separados por vírgula, exemplo: 123454,644332,2123445<br>
                              <b>Importante</b>: para participar do ranqueamento como ingressante, o aluno deve estar matriculado
                                 na habliticação do ciclo básico no jupiterweb.
                        </small>
                    </div>

                    <div class="form-group">
                        <table class="table">
                            <thead>
                                <tr>
                                <th scope="col" class="w-25">Vagas</th>
                                <th scope="col" class="w-25">Permitir ambos períodos?</th>
                                <th scope="col" class="w-50">Habilitação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($habs as $hab)
                                <tr>
                                    <td><input type="number" name="hab-{{$hab['codhab']}}" value="{{ old('hab-'.$hab['codhab'],$hab->vagas)}}" required></td>
                                    <td><input type="checkbox" name="checkbox-{{$hab['codhab']}}" value=1 
                                        {{ $hab->permite_ambos_periodos==1 ? 'checked': '' }}>
                                    </td>
                                    <td>{{ $hab->codhab }} {{ $hab->nomhab }} {{ $hab->perhab }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Salvar">
                    </div>
                </form>
        </div>
    </div>

@endsection