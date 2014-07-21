<?php

require_once __DIR__ . '/../vendor/autoload.php';

// use
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;

use Symfony\Component\Debug\Debug;

use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

use Silex\Provider\ServiceControllerServiceProvider;

$app = new Silex\Application();

// Registering
$app->register(new serviceControllerServiceProvider());
$app->register(new HttpCacheServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views',
    'cache' => __DIR__ . '/../resources/cache',
    'twig.form.templates'=> array('common/form.layout.html.twig')
));
$app->register(new TranslationServiceProvider(),  array(
    'locale_fallbacks' => array('fr')
));

// Content from content.yml
$yaml = file_get_contents(__DIR__.'/../resources/data/content.yml');
$content = Yaml::parse($yaml);

$app['twig']->addGlobal('content', $content);

// Add static pages
$pages = array(
    'home' => array(
        'url' => '/',
        'template' => 'index.html.twig'
        ),
    'interne' => array(
        'url' => '/interne',
        'template' => 'interne.html.twig'
        )
);

foreach ($pages as $route => $data) {

    $url = $data['url'];

    $app->get($url, function() use($app, $data) {

        return $app['twig']->render($data['template']);

    })
    ->bind($route);

}

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/../resources/locales/fr.yml', 'fr');
    $translator->addResource('yaml', __DIR__.'/../resources/locales/en.yml', 'en');

    return $translator;
}));

$app->match('/form', 'Ethyde\Bundle\Controller\formController::newForm')->bind('form');

// erreur
$app->error(function (\Exception $e, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    switch ($code) {
        case 404:
            $message = $app['twig']->render('errors/404.html.twig', array('error' => $e->getMessage()));
            break;
        default:
            $message = 'Shenanigans! Something went horribly wrong' . $e->getMessage();
    }

    return new Response($message, $code);
});

return $app;