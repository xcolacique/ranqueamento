@if ($resultado)
  <div class="alert alert-info">
    @if ($resultado->prioridade_eleita && $resultado->hab)
      <p class="text-muted">
        Você foi <strong>contemplado</strong> no último ranqueamento
      </p>
      <hr>
      <p><strong>Sua nota:</strong> {{ $resultado->nota }}</p>
      @if ($notaCorte)
        <p><strong>Nota de corte:</strong> {{ $notaCorte }}</p>
      @endif
      <hr>
      <p><strong>Habilitação:</strong> {{ $resultado->hab->nomhab }}</p>
      <p><strong>Período:</strong> {{ $resultado->hab->perhab }}</p>
      <p><strong>Vagas totais:</strong> {{ $resultado->hab->vagas }}</p>
      <p><strong>Status:</strong> Contemplado(a)</p>
    @else
      <p class="text-muted">
        Você participou do ranqueamento, mas <strong>não foi contemplado(a)</strong> na sua escolha.
      </p>
      <hr>
      <p><strong>Sua nota:</strong> {{ $resultado->nota }}</p>
      @if ($resultado->hab && $notaCorte)
        <p><strong>Nota de corte:</strong> {{ $notaCorte }}</p>
      @endif
      
      @if ($resultado->hab)
        <hr>
        <p><strong>Habilitação escolhida:</strong> {{ $resultado->hab->nomhab }}</p>
        <p><strong>Período:</strong> {{ $resultado->hab->perhab }}</p>
        <p><strong>Vagas totais:</strong> {{ $resultado->hab->vagas }}</p>
      @endif
      <p><strong>Status:</strong> Não contemplado(a)</p>
    @endif
  </div>
@else
  <p>Resultado ficará disponível em breve</p>
@endif