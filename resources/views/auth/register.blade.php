{{--
    Tela de cadastro de novo usuário.

    Envia POST para a rota "register" (RegisteredUserController@store),
    que delega a criação para app/Actions/Fortify/CreateNewUser.php.

    A Action valida os dados, cria o usuário com senha protegida
    por hash e registra o consentimento LGPD.
--}}

<x-layouts.guest :title="'Criar conta — Ecoa'" :subtitle="'Crie sua conta de acesso'">

    @if ($errors->any())
    <div class="alert alert-danger py-2">
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">
                Nome completo
            </label>

            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus
                autocomplete="name">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">
                E-mail
            </label>

            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required
                autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">
                Senha
            </label>

            <input id="password" type="password" name="password" class="form-control" required
                autocomplete="new-password">

            <div class="form-text">
                Mínimo de 8 caracteres.
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">
                Confirme a senha
            </label>

            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required
                autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="perfil" class="form-label">
                Perfil de acesso
            </label>

            <select id="perfil" name="perfil" class="form-select" required>
                <option value="" disabled {{ old('perfil') ? '' : 'selected' }}>
                    Selecione...
                </option>

                <option value="fonoaudiologo" {{ old('perfil') === 'fonoaudiologo' ? 'selected' : '' }}>
                    Fonoaudiólogo
                </option>

                <option value="coordenador_clinico" {{ old('perfil') === 'coordenador_clinico' ? 'selected' : '' }}>
                    Coordenador Clínico
                </option>

                <option value="administrador_ti" {{ old('perfil') === 'administrador_ti' ? 'selected' : '' }}>
                    Administrador de TI
                </option>

                <option value="responsavel_legal" {{ old('perfil') === 'responsavel_legal' ? 'selected' : '' }}>
                    Responsável Legal
                </option>
            </select>
        </div>
        <div class="mb-3 form-check">

            <input type="checkbox" name="aceite_lgpd" id="aceite_lgpd" class="form-check-input" value="1" required>

            <label for="aceite_lgpd" class="form-check-label small">
                Concordo com o tratamento dos meus dados pessoais conforme os
                <a href="{{ route('landing') }}" target="_blank" rel="noopener noreferrer"
                    class="text-decoration-none fw-semibold">
                    Termos de Uso
                </a>
                e a
                <a href="{{ route('landing') }}" target="_blank" rel="noopener noreferrer"
                    class="text-decoration-none fw-semibold">
                    Política de Privacidade
                </a>.
            </label>

            <details class="ms-4 mt-1">
                <summary class="small text-muted" style="cursor: pointer;">
                    Ver detalhes sobre o tratamento dos dados
                </summary>

                <div class="small text-muted mt-2">
                    <p class="mb-2">
                        Os dados informados serão utilizados para:
                    </p>

                    <ul class="mb-2 ps-3">
                        <li>criação e gerenciamento da conta;</li>
                        <li>autenticação e segurança de acesso;</li>
                        <li>utilização das funcionalidades do Ecoa;</li>
                        <li>gerenciamento dos registros necessários à operação da plataforma.</li>
                    </ul>

                    <p class="mb-0">
                        O consentimento é registrado com a finalidade,
                        versão do termo e data do aceite. O usuário poderá
                        exercer seus direitos sobre seus dados conforme a
                        Política de Privacidade.
                    </p>
                </div>
            </details>

        </div>

        <button type="submit" class="btn btn-ecoa w-100">
            Criar conta
        </button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-muted">
                Já tem conta? Entrar
            </a>
        </div>
    </form>

</x-layouts.guest>