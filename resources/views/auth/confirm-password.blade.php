{{--
    Confirmação de senha antes de operações sensíveis (ex: ativar/desativar
    2FA). Exigida pelo middleware "password.confirm" do Fortify — mesmo
    com a sessão já autenticada, ações críticas pedem a senha de novo.
--}}
<x-layouts.guest :title="'Confirmar senha — Ecoa'" :subtitle="'Confirme sua senha para continuar'">

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm.store') }}">
        @csrf
        <div class="mb-3">
            <label for="password" class="form-label">Senha atual</label>
            <input id="password" type="password" name="password" class="form-control"
                   required autofocus autocomplete="current-password">
        </div>
        <button type="submit" class="btn btn-ecoa w-100">Confirmar</button>
    </form>

</x-layouts.guest>