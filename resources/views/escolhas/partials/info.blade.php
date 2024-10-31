<b>Número USP:</b> {{ auth()->user()->codpes }}<br>
<b>Nome:</b> {{ auth()->user()->name }}<br>
<b>Email:</b> {{ auth()->user()->email }}<br>
<b>Período:</b> {{ \App\Service\Utils::periodo() }}<br>