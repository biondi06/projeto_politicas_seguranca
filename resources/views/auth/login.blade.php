{{--
    Tela de login.
    Envia POST para a rota "login" (login.store), que é tratada pelo
    Laravel Fortify (AuthenticatedSessionController@store).
    O Fortify já aplica, nessa rota: hash bcrypt na verificação da senha,
    rate limiting contra força bruta (5 tentativas/minuto, ver
    FortifyServiceProvider) e redirecionamento automático para a tela
    de 2FA quando o usuário tiver a verificação em duas etapas ativada.
--}}
<x-layouts.guest :title="'Entrar — Ecoa'" :subtitle="'Entre com suas credenciais'">

    {{-- Exibe erros de validação vindos do backend (ex: credenciais inválidas) --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Mensagem de status (ex: link de reset de senha enviado) --}}
    @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf {{-- Token CSRF: protege o formulário contra requisições forjadas de outros sites --}}

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required autofocus autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" type="password" name="password" class="form-control"
                   required autocomplete="current-password">
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" name="remember" id="remember" class="form-check-input">
            <label for="remember" class="form-check-label">Manter conectado</label>
        </div>

        <button type="submit" class="btn btn-ecoa w-100">Entrar</button>

        <div class="text-center mt-3">
            <a href="{{ route('password.request') }}" class="small text-muted">Esqueci minha senha</a>
        </div>
        <div class="text-center mt-2">
            <a href="{{ route('register') }}" class="small text-muted">Ainda não tem conta? Cadastre-se</a>
        </div>
    </form>

</x-layouts.guest>