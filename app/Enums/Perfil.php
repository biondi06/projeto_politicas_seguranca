<?php

namespace App\Enums;

/**
 * Os perfis de acesso do Ecoa.
 *
 * Ter os perfis num só lugar evita o erro que motivou esta classe: o texto
 * "administrador_ti" aparecia escrito à mão em vários arquivos, e um deles
 * (o formulário de cadastro) deixava qualquer pessoa escolher o próprio perfil.
 */
enum Perfil: string
{
    case Fonoaudiologo = 'fonoaudiologo';
    case CoordenadorClinico = 'coordenador_clinico';
    case AdministradorTi = 'administrador_ti';
    case ResponsavelLegal = 'responsavel_legal';

    /** Nome para mostrar na tela. */
    public function rotulo(): string
    {
        return match ($this) {
            self::Fonoaudiologo => 'Fonoaudiólogo',
            self::CoordenadorClinico => 'Coordenador Clínico',
            self::AdministradorTi => 'Administrador de TI',
            self::ResponsavelLegal => 'Responsável Legal',
        };
    }

    /** Perfis de profissionais: só podem ser obtidos por convite de um administrador. */
    public function ehProfissional(): bool
    {
        return $this !== self::ResponsavelLegal;
    }

    /**
     * Lista dos perfis que um administrador pode oferecer num convite.
     *
     * @return array<int, self>
     */
    public static function profissionais(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn (self $perfil) => $perfil->ehProfissional()
        ));
    }

    /** Perfil de quem se cadastra sozinho, sem convite. */
    public static function doCadastroAberto(): self
    {
        return self::ResponsavelLegal;
    }
}
