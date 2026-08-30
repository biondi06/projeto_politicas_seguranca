{{--
    Solicitação de recuperação de senha (requisito 2.1).
    Envia POST para "forgot-password" (PasswordResetLinkController@store),
    que gera um token assinado com expiração (requisito 2.2/2.3) e
    envia por e-mail o link de redefinição.
--}}
<x-layouts.guest :title="'Recuperar senha — Ecoa'" :subtitle="'Informe seu e-mail cadastrado'">

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email') }}" required autofocus>
        </div>

        <button type="submit" class="btn btn-ecoa w-100">Enviar link de recuperação</button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-muted">Voltar para o login</a>
        </div>
    </form>

</x-layouts.guest>