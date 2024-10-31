

<ul class="list-group">
@for($prioridade = 1; $prioridade <=7; $prioridade++)
    <li class="list-group-item">
        Opção {{ $prioridade }}: <i>{{ \App\Service\Utils::escolha($prioridade) }}</i>
    </li>
@endfor
</ul>