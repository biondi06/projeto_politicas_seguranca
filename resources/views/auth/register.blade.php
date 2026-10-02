{{--
    Cadastro de novo usuário. Valida e cria via
    app/Actions/Fortify/CreateNewUser.php — hash Argon2id na senha e
    registro de consentimento LGPD (checkbox obrigatório abaixo).
--}}
<x-layouts.guest :title="'Criar conta — Ecoa'" :subtitle="'Crie sua conta de acesso'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="auth-form-group">
            <label for="name">Nome completo</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}"
                   required autofocus autocomplete="name">
        </div>

        <div class="auth-form-group">
            <label for="email">E-mail</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}"
                   required autocomplete="username">
        </div>

        <div class="auth-form-group">
            <label for="perfil">Perfil de acesso</label>
            <select id="perfil" name="perfil" required
                    style="width:100%; padding:10px 13px; border:1px solid var(--line); border-radius:8px; font-size:14px; background:var(--paper);">
                <option value="" disabled selected>Selecione...</option>
                <option value="fonoaudiologo" {{ old('perfil') === 'fonoaudiologo' ? 'selected' : '' }}>Fonoaudiólogo</option>
                <option value="coordenador_clinico" {{ old('perfil') === 'coordenador_clinico' ? 'selected' : '' }}>Coordenador Clínico</option>
                <option value="administrador_ti" {{ old('perfil') === 'administrador_ti' ? 'selected' : '' }}>Administrador de TI</option>
                <option value="responsavel_legal" {{ old('perfil') === 'responsavel_legal' ? 'selected' : '' }}>Responsável Legal</option>
            </select>
        </div>

        <div class="auth-form-group">
            <label for="password">Senha</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            <div class="auth-form-text">Mínimo de 8 caracteres.</div>
        </div>

        <div class="auth-form-group">
            <label for="password_confirmation">Confirme a senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation"
                   required autocomplete="new-password">
        </div>

        <div class="auth-form-group" style="display:flex; align-items:flex-start; gap:8px;">
            <input type="checkbox" name="aceite_lgpd" id="aceite_lgpd" required style="width:auto; margin-top:3px;">
            <label for="aceite_lgpd" style="margin:0; font-weight:400; font-size:12.5px;">
                Li e aceito os Termos de Uso e a Política de Privacidade do Ecoa
            </label>
        </div>

        <button type="submit" class="btn-ecoa">Criar conta</button>

        <div style="text-align:center; margin-top:16px;">
            <a href="{{ route('login') }}" class="auth-link">Já tem conta? Entrar</a>
        </div>
    </form>

</x-layouts.guest>
