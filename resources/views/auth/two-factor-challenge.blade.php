{{--
    Desafio 2FA exibido após login primário, quando o usuário tem a
    verificação em duas etapas ativada (requisito 1.5/1.6).
--}}
<x-layouts.guest :title="'Verificação em duas etapas — Ecoa'" :subtitle="'Confirme sua identidade'">

    @if ($errors->any())
        <div class="auth-alert error">
            @foreach ($errors->all() as $error)
                {{ $error }}
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.login.store') }}" id="two-factor-form">
        @csrf

        <div class="auth-form-group" id="code-field">
            <label for="code">Código do aplicativo autenticador</label>
            <input id="code" type="text" name="code" inputmode="numeric" autocomplete="one-time-code" autofocus>
        </div>

        <div class="auth-form-group" id="recovery-field" style="display:none;">
            <label for="recovery_code">Código de recuperação</label>
            <input id="recovery_code" type="text" name="recovery_code">
        </div>

        <button type="submit" class="btn-ecoa">Confirmar</button>

        <div style="text-align:center; margin-top:16px;">
            <a href="#" id="toggle-recovery" class="auth-link">Perdeu acesso ao aplicativo? Usar código de recuperação</a>
        </div>
    </form>

    <script>
        document.getElementById('toggle-recovery').addEventListener('click', function (e) {
            e.preventDefault();
            const codeField = document.getElementById('code-field');
            const recoveryField = document.getElementById('recovery-field');
            codeField.style.display = codeField.style.display === 'none' ? '' : 'none';
            recoveryField.style.display = recoveryField.style.display === 'none' ? '' : 'none';
            const usingRecovery = recoveryField.style.display !== 'none';
            this.textContent = usingRecovery
                ? 'Usar o código do aplicativo autenticador'
                : 'Perdeu acesso ao aplicativo? Usar código de recuperação';
        });
    </script>

</x-layouts.guest>
