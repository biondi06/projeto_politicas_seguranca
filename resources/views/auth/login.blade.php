{{--
    Tela de login. Envia POST para "login" (login.store), tratada pelo
    Laravel Fortify: hash Argon2id na verificação da senha, rate limiting
    (5 tentativas/min, ver FortifyServiceProvider) e redirecionamento
    automático para o desafio 2FA quando ativado.
--}}
<x-layouts.guest :title="'Entrar — Ecoa'" :subtitle="'Entre com suas credenciais'">

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

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="auth-form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autofocus autocomplete="username">
        </div>

        <div class="auth-form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password"
                   required autocomplete="current-password">
        </div>

        <div class="auth-form-group" style="display:flex; align-items:center; gap:8px;">
            <input type="checkbox" name="remember" id="remember" style="width:auto;">
            <label for="remember" style="margin:0; font-weight:400;">Manter conectado</label>
        </div>

        <button type="submit" class="btn-ecoa">Entrar</button>

        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('password.request') }}" class="auth-link">Esqueci minha senha</a>
        </div>
        <div style="text-align:center; margin-top:8px;">
            <a href="{{ route('register') }}" class="auth-link">Ainda não tem conta? Cadastre-se</a>
        </div>
    </form>

</x-layouts.guest>
