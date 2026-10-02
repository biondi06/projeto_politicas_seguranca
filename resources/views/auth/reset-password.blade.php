{{--
    Tela acessada a partir do link de e-mail. Envia POST para
    "password.update" — valida token (não expirado, não usado, 2.3/2.4/2.5)
    e salva a nova senha já com hash (Argon2id).
--}}
<x-layouts.guest :title="'Nova senha — Ecoa'" :subtitle="'Defina sua nova senha'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="auth-form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
        </div>

        <div class="auth-form-group">
            <label for="password">Nova senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
        </div>

        <div class="auth-form-group">
            <label for="password_confirmation">Confirme a nova senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password">
        </div>

        <button type="submit" class="btn-ecoa">Redefinir senha</button>
    </form>

</x-layouts.guest>
