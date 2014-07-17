<?php
// src/Ethyde/Bundle/Controller/formController.php
namespace Ethyde\Bundle\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Ethyde\Bundle\Entity\Form;

class formController extends Controller
{
    public function newForm(Request $request, Application $app)
    {
        // crée une tâche et lui donne quelques données par défaut pour cet exemple
        $task = new Form();
        $task->setTask('Write a blog post');
        $task->setDueDate(new \DateTime('tomorrow'));

        $form = $this->createFormBuilder($task)
            ->add('task', 'text')
            ->add('dueDate', 'date')
            ->add('save', 'submit')
            ->getForm();

        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}