{{--
    Tela de cadastro de novo usuário.
    Envia POST para a rota "register" (RegisteredUserController@store),
    que delega a criação para app/Actions/Fortify/CreateNewUser.php.
    Nessa Action é onde o Laravel aplica o hash bcrypt na senha antes de
    salvar (Hash::make($input['password'])) — nunca guardamos a senha em
    texto puro, conforme requisito 1.1/1.3/1.4.
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
            <label for="name" class="form-label">Nome completo</label>
            <input id="name" type="text" name="name" class="form-control" value="{{ old('name') }}" required autofocus
                autocomplete="name">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">E-mail</label>
            <input id="email" type="email" name="email" class="form-control" value="{{ old('email') }}" required
                autocomplete="username">
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Senha</label>
            <input id="password" type="password" name="password" class="form-control" required
                autocomplete="new-password">
            <div class="form-text">Mínimo de 8 caracteres.</div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Confirme a senha</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required
                autocomplete="new-password">
        </div>

        <div class="mb-3">
            <label for="perfil" class="form-label">Perfil de acesso</label>
            <select id="perfil" name="perfil" class="form-select" required>
                <option value="" disabled selected>Selecione...</option>
                <option value="fonoaudiologo" {{ old('perfil') === 'fonoaudiologo' ? 'selected' : '' }}>Fonoaudiólogo
                </option>
                <option value="coordenador_clinico" {{ old('perfil') === 'coordenador_clinico' ? 'selected' : '' }}>
                    Coordenador Clínico</option>
                <option value="administrador_ti" {{ old('perfil') === 'administrador_ti' ? 'selected' : '' }}>
                    Administrador de TI</option>
                <option value="responsavel_legal" {{ old('perfil') === 'responsavel_legal' ? 'selected' : '' }}>
                    Responsável Legal</option>
            </select>
        </div>

        <button type="submit" class="btn btn-ecoa w-100">Criar conta</button>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-muted">Já tem conta? Entrar</a>
        </div>
    </form>

</x-layouts.guest>