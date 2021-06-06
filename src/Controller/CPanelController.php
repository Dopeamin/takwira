<?php

namespace App\Controller;

use App\Entity\Orders;
use Doctrine\ORM\EntityManager;
use App\Repository\UserRepository;
use App\Repository\StadeRepository;
use App\Repository\OrdersRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CPanelController extends AbstractController
{
    /**
     * @Route("/cpanel", name="cpanel")
     */
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        return $this->render('cpanel/index.html.twig', [
            'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg'
        ]);
    }
    /**
     * @Route("/cusers", name="cusers")
     */
    public function cusers(UserRepository $userrepo,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $users = $userrepo->findAll();
        $form = $this->createFormBuilder()
                ->add("Search",TextType::Class,['required'   => false])
                ->add("Submit",SubmitType::Class)
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            if($data["Search"]==null){
                $users = $userrepo->findAll();
            }else{
            $users = $userrepo->findBy(["userName"=>$data["Search"]]);
            }
        }
        
        return $this->render('cpanel/users.html.twig', [
            'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','users'=>$users,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/dusers/{id}", name="dusers")
     */
    public function dusers(UserRepository $userrepo,Request $request,int $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userrepo->find($id);
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('cusers');
    }
    /**
     * @Route("/uusers/{id}", name="uusers")
     */
    public function uusers(UserRepository $userrepo,Request $request,int $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $userrepo -> find($id);
        $page = $user->getUserName();
        $form = $this->createFormBuilder()
            ->add('userName',TextType::class,['attr'=>['placeholder'=>'Username','value'=>$user->getUserName()]])
            ->add('userPhone',IntegerType::class,['attr'=>['placeholder'=>'Phone','value'=>$user->getUserPhone()]])
            ->add('Submit',SubmitType::class,['label'=>'Apply Changes','attr'=>['class'=>'btn']])
            ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                        $user->setUserName($form->getData()['userName']);
                        $user->setUserPhone($form->getData()['userPhone']);
                                    $entityManager = $this->getDoctrine()->getManager();
                        $entityManager->persist($user);
                        $entityManager->flush();
                        return $this->redirectToRoute('cusers',['id'=>$id]);
                    
                
            }
            
        return $this->render('home/update.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/cstadiums", name="cstadiums")
     */
    public function cstadiums(StadeRepository $staderepo,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $stades = $staderepo->findAll();
        $form = $this->createFormBuilder()
                ->add("Search",TextType::Class,['required'   => false])
                ->add("Submit",SubmitType::Class)
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            if($data["Search"]==null){
                $stades = $staderepo->findAll();
            }else{
            $stades = $staderepo->findBy(["stadeName"=>$data["Search"]]);
            }
        }
        
        return $this->render('cpanel/stadiums.html.twig', [
            'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','stades'=>$stades,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/dstades/{id}", name="dstades")
     */
    public function dstade(StadeRepository $staderepo,Request $request,int $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $stade = $staderepo->find($id);
        $entityManager->remove($stade);
        $entityManager->flush();
        return $this->redirectToRoute('cstadiums');
    }
    /**
     * @Route("/ustades/{id}", name="ustades")
     */
    public function ustades(Request $request,SluggerInterface $slugger,int $id,StadeRepository $staderepo,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $stade = $staderepo->find($id);
        $page="Stadiums Add";
        $form = $this->createFormBuilder($stade)
            ->add('stadeName', TextType::class,array('label' => false,'attr'=>array('placeholder'=>'Nom Stade' )))
            ->add('stadeOwner', TextType::class,array('label' => false,'attr'=>array('placeholder'=>'Proprietaire Stade' ) ))
            ->add('password', PasswordType::class,array('label' => false,'attr'=>array('placeholder'=>'Password' ) ))
            ->add('stadeDescription', TextareaType::class,array('label' => false,'attr'=>array('placeholder'=>'Description Stade'  )))
            ->add('stadePhone', IntegerType::class,array('label' => false,'attr'=>array('placeholder'=>'Telephone')))
            ->add('adresse', TextType::class,array('label' => false,'attr'=>array('placeholder'=>'Adresse' ) ))
            ->add('stadeLocation', ChoiceType::class,[
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ben Arous' => 'Ben Arous',
                    'Ariana' => 'Ariana',
                    'Manouba' => 'Manouba'
                ],'label' => false,
                ])
            ->add('superficie', IntegerType::class,array('label' => false,'attr'=>array('placeholder'=>'Superficie' ) ))
            ->add('supplements', TextType::class,array('label' => false,'attr'=>array('placeholder'=>'Supplements' ) ))
            ->add('brochure', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('brochure2', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('brochure3', FileType::class, [
                'label' => 'Image',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '4096k',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Please upload a valid image',
                    ])
                ],
            ])
            ->add('x', NumberType::class, ['label' => 'X','attr'=>['readonly'=>'true','id'=>'x']])
            ->add('y', NumberType::class, ['label' => 'Y','attr'=>['readonly'=>'true','id'=>'y']])
            ->add('save', SubmitType::class, ['label' => 'Create Stade'])
            ->getForm();
            
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $date = new \DateTime('@'.strtotime('now'));
                $stade->setStadeDate($date);
                $stade = $form->getData();
                $encoded = $encoder->encodePassword($stade, $stade->getPassword());

                $stade->setPassword($encoded);
                $brochureFile = $form->get('brochure')->getData();
                $brochureFile2 = $form->get('brochure2')->getData();
                $brochureFile3 = $form->get('brochure3')->getData();
                $originalFilename = pathinfo($brochureFile->getClientOriginalName(), PATHINFO_FILENAME);
                $originalFilename2 = pathinfo($brochureFile2->getClientOriginalName(), PATHINFO_FILENAME);
                $originalFilename3 = pathinfo($brochureFile3->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$brochureFile->guessExtension();
                $safeFilename2 = $slugger->slug($originalFilename2);
                $newFilename2 = $safeFilename2.'-'.uniqid().'.'.$brochureFile2->guessExtension();
                $safeFilename3 = $slugger->slug($originalFilename3);
                $newFilename3 = $safeFilename3.'-'.uniqid().'.'.$brochureFile3->guessExtension();
                // Move the file to the directory where brochures are stored
                try {
                    $brochureFile->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                try {
                    $brochureFile2->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename2
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                try {
                    $brochureFile3->move(
                        $this->getParameter('brochures_directory'),
                        $newFilename3
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }
                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $stade->setBrochureFilename($newFilename);
                $stade->setBrochureFilename2($newFilename2);
                $stade->setBrochureFilename3($newFilename3);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($stade);
                $entityManager->flush();
    
                return $this->redirectToRoute('stadiums');
            }
        return $this->render('stade/add.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=> $form->createView()
        ]);
    }
    /**
     * @Route("/corders", name="corders")
     */
    public function corders(OrdersRepository $ordersrepo,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $orders = $ordersrepo->findAll();
        $form = $this->createFormBuilder()
                ->add("Search",TextType::Class,['required'   => false])
                ->add("Submit",SubmitType::Class)
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            if($data["Search"]==null){
                $orders = $ordersrepo->findAll();
            }else{
                $orders = $ordersrepo->findBy(["Stade"=>$data["Search"]]);
            }
        }
        
        return $this->render('cpanel/orders.html.twig', [
            'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$orders,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/verifyOrder/{id}", name="verifyOrder")
     */
    public function verifyOrder(int $id,OrdersRepository $ordersrepo,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $order = $ordersrepo->find($id);
        $order->setVerified(true);
        $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($order);
                $entityManager->flush();
        return $this->redirectToRoute('corders');
    }
    /**
     * @Route("/dorders/{id}", name="dorders")
     */
    public function dOrder(OrdersRepository $ordersrepo,Request $request,int $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $order = $ordersrepo->find($id);
        $entityManager->remove($order);
        $entityManager->flush();
        return $this->redirectToRoute('corders');
    }
    /**
     * @Route("/ccomments", name="ccomments")
     */
    public function ccomments(CommentsRepository $commentsrepo,Request $request): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $comments = $commentsrepo->findAll();
        $form = $this->createFormBuilder()
                ->add("Search",TextType::Class,['required'   => false])
                ->add("Submit",SubmitType::Class)
                ->getForm();
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) { 
            $data = $form->getData();
            if($data["Search"]==null){
                $comments = $commentsrepo->findAll();
            }else{
                $comments = $commentsrepo->findBy(["user"=>$data["Search"]]);
            }
        }
        
        return $this->render('cpanel/comments.html.twig', [
            'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','comments'=>$comments,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/dcomment/{id}", name="dcomment")
     */
    public function dcomment(CommentsRepository $commentsrepo,Request $request,int $id,EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $comment = $commentsrepo->find($id);
        $entityManager->remove($comment);
        $entityManager->flush();
        return $this->redirectToRoute('ccomments');
    }
}
