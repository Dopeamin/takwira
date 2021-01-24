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
            'page'=>$page,'logo'=>'assets/logo3.svg','menu'=>'assets/menu.svg'
        ]);
    }
    /**
     * @Route("/login", name="login")
     */
    public function login(): Response
    {
        $page="Log In";
        return $this->render('home/login.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.svg','menu'=>'assets/menu2.svg'
        ]);
    }
    /**
     * @Route("/register", name="register")
     */
    public function register(): Response
    {
        $page="Sign Up";
        return $this->render('home/register.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.svg','menu'=>'assets/menu2.svg'
        ]);
    }
    /**
     * @Route("/stadiums", name="stadiums")
     */
    public function Stadiums(): Response
    {
        $page="Stadiums";
        return $this->render('home/stadiums.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.svg','menu'=>'assets/menu2.svg'
        ]);
    }
}
