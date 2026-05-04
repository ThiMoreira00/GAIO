<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Illuminate\Cache\FileStore;
use Illuminate\Cache\Repository;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Translation\FileLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\DatabasePresenceVerifier;
use Illuminate\Validation\Factory;

try {
    
    // Configuração do banco de dados
    $capsule = new Capsule();
    $capsule->addConnection([
        'driver' => $_ENV['BANCO_DADOS_DRIVER'],
        'host' => $_ENV['BANCO_DADOS_SERVIDOR'],
        'database' => $_ENV['BANCO_DADOS_NOME'],
        'username' => $_ENV['BANCO_DADOS_USUARIO'],
        'password' => $_ENV['BANCO_DADOS_SENHA'],
        'charset' => $_ENV['BANCO_DADOS_CHARSET'],
        'collation' => $_ENV['BANCO_DADOS_COLLATION'],
        'prefix' => 'gaio_',
    ]);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    // Configuração do cache
    $cacheDiretorio = __DIR__ . '/../storage/cache';
    if (!file_exists($cacheDiretorio)) {
        mkdir($cacheDiretorio, 0755, true);
    }

    $filesystem = new Filesystem();
    $fileStore = new FileStore($filesystem, $cacheDiretorio);
    $cache = new Repository($fileStore);

    // Configuração do container
    $container = Container::getInstance();
    $container->singleton('cache', static function () use ($cache) {
        return $cache;
    });

    $container->singleton('cache.store', static function () use ($cache) {
        return $cache;
    });

    $container->singleton('db', static function () use ($capsule) {
        return $capsule->getDatabaseManager();
    });

    Facade::clearResolvedInstances();
    /** @var \Illuminate\Contracts\Foundation\Application $containerApp */
    $containerApp = $container;
    Facade::setFacadeApplication($containerApp);

    // Configuração da validação
    $loader = new FileLoader($filesystem, '');
    $translator = new Translator($loader, 'pt_BR');
    $validator = new Factory($translator);
    $presenceVerifier = new DatabasePresenceVerifier($capsule->getDatabaseManager());
    $validator->setPresenceVerifier($presenceVerifier);

    return $validator;
} catch (Exception $e) {
    error_log('Erro de conexão com o banco de dados: ' . $e->getMessage());
    return null;
}
