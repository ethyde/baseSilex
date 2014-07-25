<?php

// use
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use Silex\Provider\FormServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;

use Silex\Provider\ServiceControllerServiceProvider;

$app = new Silex\Application();

// Registering
$app->register(new ServiceControllerServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TwigServiceProvider());

$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('fr')
));

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/../resources/locales/fr.yml', 'fr');

    $translator->addResource('yaml', __DIR__.'/../resources/locales/en.yml', 'en');

    return $translator;
}));

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {

    // Content from content.yml
    $yaml = file_get_contents(__DIR__.'/../resources/data/content.yml');
    $content = Yaml::parse($yaml);

    // http://silex.sensiolabs.org/doc/providers/twig.html#customization
    // https://github.com/silexphp/Silex-Skeleton/blob/master/src/app.php
    $twig->addGlobal('content', $content);

    return $twig;
}));

return $app;