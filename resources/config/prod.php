<?php

// configure your app for the production environment

$app['twig.path'] = array(__DIR__.'/../../resources/views');
$app['twig.options'] = array('cache' => __DIR__.'/../../resources/cache');
$app['twig.form.templates'] = array('common/form.layout.html.twig');