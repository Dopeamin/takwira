<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $page="Home";
        return $this->render('home/index.html.twig',[
            'page'=>$page,'logo'=>'assets/logo3.svg'
        ]);
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        $page="Login";
        return $this->render('home/login.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.svg'
        ]);
    }
}
