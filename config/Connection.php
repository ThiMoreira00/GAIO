<?php

use Illuminate\Database\Capsule\Manager as Capsule;

try {
    $capsuleDB = new Capsule;

    $capsuleDB->addConnection([
        'driver'    => $_ENV['BANCO_DADOS_DRIVER'],
        'host'      => $_ENV['BANCO_DADOS_SERVIDOR'],
        'database'  => $_ENV['BANCO_DADOS_NOME'],
        'username'  => $_ENV['BANCO_DADOS_USUÁRIO'],
        'password'  => $_ENV['BANCO_DADOS_SENHA'],
        'charset'   => $_ENV['BANCO_DADOS_CHARSET'],
        'collation' => $_ENV['BANCO_DADOS_COLLATION'],
        'prefix'    => 'gaio_',
    ]);

    $capsuleDB->setAsGlobal();
    $capsuleDB->bootEloquent();

    // Verificar se a instância está funcionando
    if (!$capsuleDB::connection()->getPdo()) {
        throw new Exception("Não foi possível estabelecer conexão com o banco de dados.");
    }

    return $capsuleDB;

} catch (Exception $e) {
    error_log("Erro de conexão com o banco de dados: " . $e->getMessage());
    return null;
}