<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    /*public function register(Request $request,UserRepository $userrepo,UserPasswordEncoderInterface $encoder): Response
    {   
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $use=$form->getData();
            
                    $pass= $user->getUserPass();
                    $hash = $encoder->encodePassword($user , $pass);
                    $user->setUserPass($hash);
                    $manager=$this->getDoctrine()->getManager();
                    $manager->persist($user);
                    $manager->flush(); 

                    return $this->redirect('login');
                
            
            
        }
        $page="Sign Up";
        return $this->render('home/register.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form' => $form->createView()
        ]);
    }*/
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
        $error = $authenticationUtils->getLastAuthenticationError();
        $page="Log In";
        return $this->render('home/login.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','error' => $error
        ]);
    }
    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        
    }
}
