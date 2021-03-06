#!/usr/bin/env php
<?php

use TallTree\Roots\Service\Database\Connection;
use TallTree\Roots\Service\Database\PdoFactory;
use TallTree\Roots\Service\Database\Query;
use TallTree\Roots\Service\File\Handle;
use TallTree\Roots\Patch\Model\Service\Database\MySqlMap;
use TallTree\Roots\Patch\Model\Service\File\FileMap;
use TallTree\Roots\Patch\Factory;
use TallTree\Roots\Patch\Repository;
use TallTree\Roots\Patch\Patcher;
use TallTree\Roots\Patch\FilterFactory;
use TallTree\Roots\Install\Repository as InstallRepository;
use TallTree\Roots\Install\Model\Service\File\FileMap as InstallFileMap;
use TallTree\Roots\Install\Model\Service\Database\MySqlMap as InstallMySqlMap;
use TallTree\Roots\Install\Factory as InstallFactory;
use TallTree\Roots\Install\Installer;
use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . '/../../vendor/autoload.php';

$adapter = new Local(__DIR__ . '/../../');
$fileSystem = new Filesystem($adapter, [
    'visibility' => AdapterInterface::VISIBILITY_PRIVATE
]);

$configurationFile = $fileSystem->read('testing/Dev/conf.json');
$configuration = json_decode($configurationFile, true);
//$configuration = Yaml::parse($configurationFile);

$database = $configuration['database'];
$dbDir = $configuration['directory'];

$connection = new Connection(
    new PdoFactory(),
    $database['type'],
    $database['server'],
    $database['username'],
    $database['password'],
    $database['name']
);

$query = new Query($connection);
$fileHandle = new Handle($fileSystem, $dbDir);
$dbMap = new MySqlMap($query);
$fileMap = new FileMap($fileSystem, $dbDir);
$factory = new Factory();
$filterFactory = new FilterFactory();
$repository = new Repository($dbMap, $fileMap, $factory);

$installDbMap = new InstallMySqlMap($query);
$installFileMap = new InstallFileMap($fileSystem, $dbDir);
$installFactory = new InstallFactory();
$installRepository = new InstallRepository($installDbMap, $installFileMap, $installFactory);
$installer = new Installer(
    $installRepository,
    $query,
    $fileHandle,
    $installDbMap,
    $installFileMap,
    $installFactory
);

$worker = new Patcher(
    $repository,
    $filterFactory,
    $query,
    $fileHandle,
    $dbMap,
    $fileMap,
    $installRepository,
    $installer
);

$worker->patchAll();

