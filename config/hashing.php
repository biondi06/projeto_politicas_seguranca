<?php

return [
    // Define o algoritmo utilizado para proteger as senhas.
    // O valor pode ser configurado pelo ambiente através de HASH_DRIVER,
    // utilizando Argon2id como padrão da aplicação.
    'driver' => env('HASH_DRIVER', 'argon2id'),

];