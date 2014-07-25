<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

$console = new Application('My Silex Application', 'n/a');

$console->getDefinition()->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'The Environment name.', 'dev'));
$console->setDispatcher($app['dispatcher']);
$console
    ->register('cache:clear')
    ->setDescription('Clears the cache')
    ->setCode(function (InputInterface $input, OutputInterface $output) use ($app) {

        $cacheDir = $app['cache.path'];
        $finder = Finder::create()->in($cacheDir)->notName('.gitkeep');

        $filesystem = new Filesystem();
        $filesystem->remove($finder);

        $output->writeln(sprintf("%s <info>success</info>", 'cache:clear'));
    });
;

return $console;