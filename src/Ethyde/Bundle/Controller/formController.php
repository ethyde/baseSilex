<?php
// src/Ethyde/Bundle/Controller/formController.php
namespace Ethyde\Bundle\Controller;

use Ethyde\Bundle\Entity\Form;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;

class formController
{
    public function newForm(Request $request, Application $app)
    {
        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $task = new Form();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $app['form.factory']->createBuilder('form')
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->add('save', 'submit')
            ->getForm();

        return $app['twig']->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}