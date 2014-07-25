<?php

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

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

// form controller
$app->match('/form', 'Ethyde\Bundle\Controller\formController::newForm' )->bind('form');

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