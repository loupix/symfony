<?php

namespace Shoefony\CmsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;
use Shoefony\CmsBundle\Entity\Contact;
use Shoefony\CmsBundle\Form\ContactType;


class MainController extends Controller
{
    /**
     * @Route("/", name="shoefony_cms_homepage")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $lastProducts = $em->getRepository("ShoefonyStoreBundle:Product")->lastProduct();
        $popularProducts = $em->getRepository("ShoefonyStoreBundle:Product")->getMostCommentaire();
        $brands = $em->getRepository("ShoefonyStoreBundle:Brand")->findAll();
        $slides = $em->getRepository("ShoefonyStoreBundle:Slide")->getLast();
        return $this->render('ShoefonyCmsBundle:Main:index.html.twig', Array(
                "brands"=>$brands,
                "popularProducts"=>$popularProducts,
                "lastProducts"=>$lastProducts,
                "slides"=>$slides));
    }

    /**
     * @Route("/presentation", name="shoefony_cms_presentation")
     */
    public function presentationAction()
    {
        return $this->render('ShoefonyCmsBundle:Main:presentation.html.twig');
    }


    /**
     * @Route("/contact", name="shoefony_cms_contact")
     */
    public function contactAction(Request $request)
    {
        $contact = new Contact();
        $form = $this->createForm(new ContactType(), $contact);
        if($request->isMethod("POST")){
            $form->bind($request);
            if($form->isValid()){


                // Sauvegarde message
                $em = $this->getDoctrine()->getManager();

                $contact = $form->getData();

                $em->persist($contact);
                $em->flush();

                $message = \Swift_Message::newInstance()
                    ->setSubject("Nouvelle demande de contact")
                    ->setFrom("contact@shoefony.fr")
                    ->setTo("administrateur@shoefony.fr")
                    ->setBody($this->renderView('ShoefonyCmsBundle:Mail:email.html.twig'));

                $this->get("mailer")->send($message);


                $this->get("session")->getFlashBag()->add('notice', 'Merci de votre message !');
                return $this->redirect($this->generateUrl('shoefony_cms_contact'));
            }
        }
        return $this->render('ShoefonyCmsBundle:Main:contact.html.twig', Array(
            'form'=>$form->createView()
        ));
    }
}
