<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    
    /**
     * Register new User
     * 
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $encoder)    {
               
        $user = new User();
        $form = $this->createForm(UserType::class, $user);        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {          
            
            $plainPassword = $form->getData()->getPassword();
            $encoded = $encoder->encodePassword($user, $plainPassword);        
            $user->setPassword($encoded);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);            
            $em->flush();
            
            return $this->redirectToRoute('artists');       
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }   

    /**
     * @Route("/", name="homepage")
     */
    public function homepage()    {      
        return $this->redirectToRoute('artists');       
    }
}
