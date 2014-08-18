<?php

// Cache
$app['cache.path'] = __DIR__ . '/../cache';

$app['http_cache.cache_dir'] = $app['cache.path'] . '/http';
$app['twig.options.cache'] = $app['cache.path'] . '/twig';
$app['profiler.cache_dir'] =  $app['cache.path'] . '/profiler';

// configure your app for the production environment

// config Twig
$app['twig.path'] = array(__DIR__.'/../views');
$app['twig.form.templates'] = array('common/form.layout.html.twig');