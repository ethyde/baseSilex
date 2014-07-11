<?php

$app = require __DIR__.'/../src/app.php';

// TODO Commenter la ligne suivante en prod
$app['debug'] = true;

$app->run();