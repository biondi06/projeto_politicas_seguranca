{{--
    Solicitação de recuperação de senha (requisito 2.1). Envia POST para
    "forgot-password" (PasswordResetLinkController@store), que gera um
    token assinado com expiração (2.2/2.3) e envia o link por e-mail.
--}}
<x-layouts.guest :title="'Recuperar senha — Ecoa'" :subtitle="'Informe seu e-mail cadastrado'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    @if (session('status'))
        <div class="auth-alert success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="auth-form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="btn-ecoa">Enviar link de recuperação</button>

        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('login') }}" class="auth-link">Voltar para o login</a>
        </div>
    </form>

</x-layouts.guest>
