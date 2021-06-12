<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Stade;
use App\Repository\UserRepository;
use App\Repository\StadeRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $page="Home";
        return $this->render('home/index.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.png','menu'=>'assets/menu.svg'
        ]);
    }
    
    
    
    /**
     * @Route("/profile/{id}", name="profile")
     */
    public function profile(int $id,Request $request,UserRepository $userrepo) : Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        $user = $userrepo -> find($id);
        $user2 = $this->getUser();
        $page = $user->getUserName();
        if($user->getId()!=$user2->getId()){
            return $this->redirectToRoute('home');
        }
        return $this->render('home/profile.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg',
        ]);
    }
    /**
     * @Route("/profile/{id}/update", name="update")
     */
    public function profileUpdate(int $id,Request $request,UserRepository $userrepo,UserPasswordEncoderInterface $passwordEncoder) : Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $userrepo -> find($id);
        
        $user2 = $this->getUser();
        $page = $user->getUserName();
        if($user->getId()!=$user2->getId()){
            return $this->redirectToRoute('home');
        }
        $pass2= $user->getUserPass();
        $form = $this->createFormBuilder($user, [
            'validation_groups' => ['update'],
        ])
            ->add('userName',TextType::class,['attr'=>['placeholder'=>'Username']])
            ->add('userPhone',IntegerType::class,['attr'=>['placeholder'=>'Phone']])
            ->add('oldPassword',PasswordType::class,['label'=>'Password','attr'=>['placeholder'=>'Password']])
            ->add('Submit',SubmitType::class,['label'=>'Apply Changes','attr'=>['class'=>'btn']])
            ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                /*
                if($pass == $user2->getUserPass()){
                    $user->setUserPass($passwordEncoder->encodePassword(
                        $user,
                        $form->get('userPass')->getData()
                    ));*/
                        $entityManager->persist($user);
                        $entityManager->flush();
                        return $this->redirectToRoute('profile',['id'=>$id]);
                    
                //};
            }
            
        return $this->render('home/update.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/profile/{id}/changePass", name="changePass")
     */
    public function passChange(int $id,Request $request,UserRepository $userrepo,UserPasswordEncoderInterface $passwordEncoder) : Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $userrepo -> find($id);
        
        $user2 = $this->getUser();
        $page = $user->getUserName();
        if($user->getId()!=$user2->getId()){
            return $this->redirectToRoute('home');
        }
        $form = $this->createFormBuilder($user)
            ->add('oldPassword',PasswordType::class,['attr'=>['placeholder'=>'Old Password'],'label'=>'Old Password'])
            ->add('userPassNew',PasswordType::class,['attr'=>['placeholder'=>'New Password'],'label'=>'New Password'])
            ->add('Submit',SubmitType::class,['label'=>'Apply Changes','attr'=>['class'=>'btn']])
            ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $entityManager = $this->getDoctrine()->getManager();
                $user->setUserPass(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('userPassNew')->getData()
                    )
                );
                        $entityManager->persist($user);
                        $entityManager->flush();
                    
                        return $this->redirectToRoute('profile',['id'=>$id]);
            }
            
        return $this->render('home/update.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/profile/{id}/orders", name="userOrders")
     */
    public function userOrders(int $id,Request $request,UserRepository $userrepo,UserPasswordEncoderInterface $passwordEncoder) : Response {
        $this->denyAccessUnlessGranted('ROLE_USER');
        
        $user = $userrepo -> find($id);
        
        $user2 = $this->getUser();
        $page = $user->getUserName();
        if($user->getId()!=$user2->getId()){
            return $this->redirectToRoute('home');
        }
        $Orders = $user->getOrders();
        $rdv = [];
            foreach($Orders as $orderss){
                $title = "Ref de stade : ".$orderss->getStade()->getId()." Ref : ". $orderss->getId();
                $rdv[]= [
                    'id'=> $orderss->getId(),
                    'start'=> $orderss->getStartDate()->format('Y-m-d H:i:s'),
                    'end'=> $orderss->getEndDate()->format('Y-m-d H:i:s'),
                    'title'=>$title,
                    'backgroundColor'=>'lightgreen'

                ];
            };
            $date=json_encode($rdv);    
        return $this->render('home/orders.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$Orders,'data'=>compact('date')
        ]);
    }
    /**
     * @Route("/aboutus", name="aboutus")
     */
    public function aboutus(): Response
    {
        $page="About Us";
        return $this->render('home/aboutus.html.twig',[
            'page'=>$page,'logo'=>'assets/logo.png','menu'=>'assets/menu2.svg'
        ]);
    }
    
    
}

