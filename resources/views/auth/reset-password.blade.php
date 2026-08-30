{{--
    Tela acessada a partir do link de e-mail (rota com {token}).
    Envia POST para "password.update" (NewPasswordController@store), que
    valida se o token ainda não expirou e não foi usado antes (requisito
    2.3/2.4/2.5) e então salva a nova senha já com hash (bcrypt).
--}}
<x-layouts.guest :title="'Nova senha — Ecoa'" :subtitle="'Defina sua nova senha'">

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        {{-- Token de recuperação: vem embutido na própria URL do link enviado por e-mail --}}
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control"
                   value="{{ old('email', $request->email) }}" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Nova senha</label>
            <input id="password" type="password" name="password" class="form-control"
                   required autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirme a nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   class="form-control" required autocomplete="new-password">
        </div>

        <button type="submit" class="btn btn-ecoa w-100">Redefinir senha</button>
    </form>

</x-layouts.guest>