@if(\App\Services\Utils::declinou())
    <form class="form-inline" method="POST" action="{{ route('declinar') }}">
        @csrf
        <b>Declinou do português?</b>&nbsp;sim&nbsp;
        <button type="submit"  role="form"class="btn btn-warning"  name="declinar" value=0 
        onclick="return confirm('Tem certeza que deseja cancelar a declinação do português?');"> Cancelar declinação 
        </button>
    </form>
@else 
    <form class="form-inline" method="POST" action="{{ route('declinar') }}">
        @csrf
        <b>Declinou do português?</b>&nbsp;não&nbsp; 
        <button type="submit" class="btn btn-warning" name="declinar" value=1 
        onclick="return confirm('Tem certeza que deseja declinar do português?');"> Quero declinar 
        </button>
    </form>
@endif