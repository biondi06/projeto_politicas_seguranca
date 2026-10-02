{{--
    Confirmação de senha antes de operações sensíveis (ex: ativar/desativar
    2FA). Exigida pelo middleware "password.confirm" do Fortify.
--}}
<x-layouts.guest :title="'Confirmar senha — Ecoa'" :subtitle="'Confirme sua senha para continuar'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <div class="auth-form-group">
            <label for="password">Senha atual</label>
            <input id="password" type="password" name="password" required autofocus autocomplete="current-password">
        </div>
        <button type="submit" class="btn-ecoa">Confirmar</button>
    </form>

</x-layouts.guest>
