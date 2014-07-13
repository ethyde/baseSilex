<?php

require_once __DIR__ . '/../vendor/autoload.php';

// use
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Loader\YamlFileLoader;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraints as Assert;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\HttpCacheServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

$app = new Silex\Application();

// Registering
$app->register(new HttpCacheServiceProvider());
$app->register(new FormServiceProvider());
$app->register(new SessionServiceProvider());
$app->register(new UrlGeneratorServiceProvider());
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../resources/views',
    'cache' => __DIR__ . '/../resources/cache',
    'twig.form.templates'=> array('common/form.layout.html.twig')
));
$app->register(new ValidatorServiceProvider());
$app->register(new TranslationServiceProvider());

// Content from content.yml
$yaml = file_get_contents(__DIR__.'/../resources/data/content.yml');
$content = Yaml::parse($yaml);

// Add static pages
$pages = array(
    'home' => array(
        'url' => '/',
        'template' => 'index.html.twig',
        'content' => $content
        ),
    'interne' => array(
        'url' => '/interne',
        'template' => 'interne.html.twig',
        'content' => $content
        )
);

foreach ($pages as $route => $data) {

    $url = $data['url'];

    $app->get($url, function() use($app, $data) {

        return $app['twig']->render($data['template'], array(
            'content' => $data['content']
        ));

    })
    ->value('_locale', 'fr')
    ->bind($route);

}

$app['translator'] = $app->share($app->extend('translator', function ($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', __DIR__.'/locales/fr.yml', 'fr');

    return $translator;
}));

$app->match('/form', function (Request $request) use ($app, $content) {

    $choices = array('choice a', 'choice b', 'choice c');
    $builder = $app['form.factory']->createBuilder('form');

    $form = $builder
        ->add(
            $builder->create('sub-form', 'form')
                ->add('subformemail1', 'email', array(
                    'constraints' => array(new Assert\NotBlank(), new Assert\Email()),
                    'attr' => array('placeholder' => 'email constraints'),
                    'label' => 'A custom label : ',
                ))
                ->add('subformtext1', 'text')
        )
        ->add('text1', 'text', array(
            'constraints' => new Assert\NotBlank(),
            'attr' => array('placeholder' => 'not blank constraints')
        ))
        ->add('text2', 'text', array('required' => false, 'attr' => array('class' => 'span1', 'placeholder' => '.span1')))
        ->add('text3', 'text', array('attr' => array('class' => 'span2', 'placeholder' => '.span2')))
        ->add('text4', 'text', array('attr' => array('class' => 'span3', 'placeholder' => '.span3')))
        ->add('text5', 'text', array('attr' => array('class' => 'span4', 'placeholder' => '.span4')))
        ->add('text6', 'text', array('attr' => array('class' => 'span5', 'placeholder' => '.span5')))
        ->add('text8', 'text', array('disabled' => true, 'attr' => array('placeholder' => 'disabled field')))
        ->add('textarea', 'textarea')
        ->add('email', 'email', array('required' => false) )
        ->add('integer', 'integer')
        ->add('money', 'money')
        ->add('number', 'number')
        ->add('password', 'password')
        ->add('percent', 'percent')
        ->add('search', 'search')
        ->add('url', 'url')
        ->add('choice1', 'choice', array(
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true
        ))
        ->add('choice2', 'choice', array(
            'choices' => $choices,
            'multiple' => false,
            'expanded' => true
        ))
        ->add('choice3', 'choice', array(
            'choices' => $choices,
            'multiple' => true,
            'expanded' => false
        ))
        ->add('choice4', 'choice', array(
            'choices' => $choices,
            'multiple' => false,
            'expanded' => false
        ))
        ->add('country', 'country')
        ->add('language', 'language')
        ->add('locale', 'locale')
        ->add('timezone', 'timezone')
        ->add('date', 'date')
        ->add('datetime', 'datetime')
        ->add('time', 'time')
        ->add('birthday', 'birthday')
        ->add('checkbox', 'checkbox')
        ->add('file', 'file')
        ->add('radio', 'radio')
        ->add('password_repeated', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'options' => array('required' => true),
            'first_options' => array('label' => 'Password'),
            'second_options' => array('label' => 'Repeat Password'),
        ))
        ->add('submit', 'submit', array('label' => 'Envoyer'))
        ->getForm();

    $form->handleRequest($request);
    if ($form->isSubmitted()) {
        if ($form->isValid()) {
            $app['session']->getFlashBag()->add('success', array(
                    'title'   => 'Success !',
                    'message' => 'Le formulaire est bien validé',
                )
            );
        } else {
            $form->addError(new FormError('This is a global error'));
            $app['session']->getFlashBag()->add('info', array(
                    'title'   => 'Error !',
                    'message' => 'Le formulaire n\'est pas valide',
                )
            );
        }
    }

    return $app['twig']->render('form.html.twig', array(
        'form' => $form->createView(),
        'content' => $content
        ));
})->bind('form');

// erreur
// $app->error(function (\Exception $e, $code) use ($app) {
//     if ($app['debug']) {
//         return;
//     }

//     switch ($code) {
//         case 404:
//             $message = $app['twig']->render('errors/404.html.twig', array('error' => $e->getMessage()));
//             break;
//         default:
//             $message = 'Shenanigans! Something went horribly wrong' . $e->getMessage();
//     }

//     return new Response($message, $code);
// });

return $app;