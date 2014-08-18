<?php

require_once __DIR__ . '/../vendor/autoload.php';

require __DIR__ . '/../src/app.php';

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

$console = new Application('Silex', '0.1');

require __DIR__.'/../resources/config/prod.php';

// $app->boot();

$console
    ->register('cache:clear')
    ->setDescription('Clear the cache')
    ->setCode(function( InputInterface $input, OutputInterface $output ) use ($app) {
        $cacheDir = $app['cache.path'];
        $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

        $filesystem = new Filesystem();
        $filesystem->remove($finder);

        $output->writeln('<info>Cache cleared</info>');
    });

$console->run();
