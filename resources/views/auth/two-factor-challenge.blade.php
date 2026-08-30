{{--
    Tela exibida após o login primário (e-mail+senha), quando o usuário
    tem a verificação em duas etapas ativada (requisito 1.5/1.6).
    Envia POST para "two-factor.login.store". O usuário pode informar o
    código de 6 dígitos do app autenticador OU, se perdeu o acesso, um
    dos códigos de recuperação gerados na ativação do 2FA.
--}}
<x-layouts.guest :title="'Verificação em duas etapas — Ecoa'" :subtitle="'Confirme sua identidade'">

    @if ($errors->any())
        <div class="alert alert-danger py-2">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('two-factor.login.store') }}" id="two-factor-form">
        @csrf

        <div class="mb-3" id="code-field">
            <label for="code" class="form-label">Código do aplicativo autenticador</label>
            <input id="code" type="text" name="code" class="form-control"
                   inputmode="numeric" autocomplete="one-time-code" autofocus>
        </div>

        <div class="mb-3 d-none" id="recovery-field">
            <label for="recovery_code" class="form-label">Código de recuperação</label>
            <input id="recovery_code" type="text" name="recovery_code" class="form-control">
        </div>

        <button type="submit" class="btn btn-ecoa w-100">Confirmar</button>

        <div class="text-center mt-3">
            {{-- Alterna entre digitar o código do app ou um código de recuperação --}}
            <a href="#" id="toggle-recovery" class="small text-muted">Perdeu acesso ao aplicativo? Usar código de recuperação</a>
        </div>
    </form>

    <script>
        // Alterna entre os dois modos de verificação sem precisar de duas telas separadas
        document.getElementById('toggle-recovery').addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('code-field').classList.toggle('d-none');
            document.getElementById('recovery-field').classList.toggle('d-none');
            const usingRecovery = !document.getElementById('recovery-field').classList.contains('d-none');
            this.textContent = usingRecovery
                ? 'Usar o código do aplicativo autenticador'
                : 'Perdeu acesso ao aplicativo? Usar código de recuperação';
        });
    </script>

</x-layouts.guest>