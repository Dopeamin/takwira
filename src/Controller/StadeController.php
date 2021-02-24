<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Stade;
use App\Entity\Orders;
use App\Entity\Reviews;
use App\Entity\Comments;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\FloatType;
use App\Repository\StadeRepository;
use App\Repository\OrdersRepository;
use App\Repository\ReviewsRepository;
use App\Repository\CommentsRepository;
use Doctrine\ORM\EntityManagerInterface;
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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class StadeController extends AbstractController
{
    public $page=false;
    /**
     * @Route("/stadiums", name="stadiums")
     */
    public function Stadiums(ReviewsRepository $ratingrepo,Request $request,StadeRepository $staderepo,PaginatorInterface $paginator): Response
    {
        $featured = $staderepo
        ->findOneBy(['featured'=>true]);
        $city=$request->query->getAlpha('City', 'All');
        if($city=="All"){
            $stades = $staderepo
            ->findAll(['stadeRating'=>'DESC']);
            
        }else{
            $stades = $staderepo
            ->findBy(['stadeLocation'=>$city],['stadeRating'=>'DESC']);
            
        }
        
            $articles = $paginator->paginate(
                $stades, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
        $form = $this->createFormBuilder()
        ->add('search',TextType::class,['required'=>false,'attr'=> ['class'=>'search','placeholder'=>'search'],'label'=>false])
        ->add('submit',SubmitType::class)
        ->getform();
        $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $request->request->get('form');
                $page = $data['search'];
                $stades = $staderepo
                    ->findAl($page,$city);
            }   
            $articles = $paginator->paginate(
                $stades, // Requête contenant les données à paginer (ici nos articles)
                $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
                10 // Nombre de résultats par page
            );
        $page="Stadiums";
        foreach($stades as $sta){
            $ratings = $ratingrepo->findBy(['stade'=>$sta]);
            $average = 0;
            $i=0;
            foreach($ratings as $rating){
                $average += $rating->getRating();
                $i++;
            }
            if($i!=0){
                $average = $average/$i;
            }
            if($sta->getStadeRating() != $average){
                $sta->setStadeRating($average);
                $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($sta);
                            $entityManager->flush();
            }
            
        }
        
        return $this->render('stade/stadiums.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','stade'=>$articles,'form'=>$form->createView(),'city'=>$city,'featured'=>$featured
        ]);
    }
    /**
     * @Route("/stadiums/{id}/cpanel", name="stadiumCpanel")
     */
    public function StadiumPanel(Request $request,int $id,UserPasswordEncoderInterface $encoder,StadeRepository $staderepo): Response
    {
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
        $stade = $staderepo->find($id);
        $form=$this->createFormBuilder()
            ->add("Password",PasswordType::class)
            ->add("Submit",SubmitType::class)
            ->getForm();
        $page=false;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
            if($encoded == $stade->getPassword()){
                $page=$encoded;
                
            }else{
                $this->addFlash('failure', 'Wrong Password');
            }
        }
        return $this->render('stade/cpanel.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=>$form->createView(),'stade'=>$stade
        ]);
    }
    /**
     * @Route("/stadiums/{id}/cpanel/sstade", name="stadiumUpdate")
     */
    public function StadiumUpdate(Request $request,SluggerInterface $slugger,UserPasswordEncoderInterface $encoder,StadeRepository $staderepo,int $id): Response
    {
        $stade = $staderepo->find($id);
        $page="Stadiums Add";
        $form = $this->createFormBuilder($stade)
            ->add('stadeName', TextType::class,array('attr'=>array('placeholder'=>'Nom Stade' )))
            ->add('stadeOwner', TextType::class,array('attr'=>array('placeholder'=>'Proprietaire Stade' ) ))
            ->add('password', PasswordType::class,array('attr'=>array('placeholder'=>'Password' ) ))
            ->add('stadeDescription', TextareaType::class,array('attr'=>array('placeholder'=>'Description Stade'  )))
            ->add('stadePhone', IntegerType::class,array('attr'=>array('placeholder'=>'Telephone')))
            ->add('adresse', TextType::class,array('attr'=>array('placeholder'=>'Adresse' ) ))
            ->add('stadeLocation', ChoiceType::class,[
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ben Arous' => 'Ben Arous',
                    'Ariana' => 'Ariana',
                    'Manouba' => 'Manouba'
                ],'label' => false,
                ])
            ->add('superficie', IntegerType::class,array('attr'=>array('placeholder'=>'Superficie' ) ))
            ->add('supplements', TextType::class,array('attr'=>array('placeholder'=>'Supplements' ) ))
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
            ->add('save', SubmitType::class, ['label' => 'Update Stade'])
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
    
                return $this->redirectToRoute('stadiumCpanel');
            }
        return $this->render('stade/add.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','form'=> $form->createView()
        ]);
    }
    /**
     * @Route("/stadium/{id}/comments", name="stadcomments")
     */
    public function ccomments(int $id,CommentsRepository $commentsrepo,Request $request,StadeRepository $staderepo,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');
        $stade= $staderepo->find($id);
        $comments = $commentsrepo->findBy(['stadeId'=>$id]);
        $form=$this->createFormBuilder()
            ->add("Password",PasswordType::class)
            ->add("Submit",SubmitType::class)
            ->getForm();
        $page=false;
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            
            $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
            if($encoded == $stade->getPassword()){
                $page=$encoded;
            }else{
                $this->addFlash('failure', 'Wrong Password');
            }
        }
        
        return $this->render('stade/comments.html.twig', [
        'page'=>$page,'stade'=>$stade,'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','comments'=>$comments,'form'=>$form->createView()
        ]);
    }
     /**
     * @Route("/stadiums/{id}/cpanel/sorders", name="stadiumOrders")
     */
    public function sorders(StadeRepository $staderepo,Request $request,$id,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
        $stade = $staderepo->find($id);
        $form=$this->createFormBuilder()
            ->add("Password",PasswordType::class)
            ->add("Submit",SubmitType::class)
            ->getForm();
        $form->handleRequest($request);
        $rdv = [];
        $orders = new Orders();
        if($form->isSubmitted() && $form->isValid()){
            
            $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
            if($encoded == $stade->getPassword()){
                $this->page=$encoded;
                $orders = $stade->getOrders();
                
                    foreach($orders as $order){
                        $rdv[]= [
                            'id'=> $order->getId(),
                            'start'=> $order->getStartDate()->format('Y-m-d H:i:s'),
                            'end'=> $order->getEndDate()->format('Y-m-d H:i:s'),
                            'title'=>$order->getUser()->getUserName(),
                            'backgroundColor'=>'lightgreen'

                        ];
                };
            }else{
                $this->addFlash('failure', 'Wrong Password');
            }
        }
        $date = json_encode($rdv);
        return $this->render('stade/orders.html.twig', [
            'page'=>$this->page,'data'=>compact('date'),'stade'=>$stade,'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$orders,'form'=>$form->createView()
        ]);
    }
    /**
     * @Route("/stadiums/add", name="stadiumsAdd")
     */
    public function StadiumAdd(Request $request,SluggerInterface $slugger,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("ROLE_ADMIN");
        $stade=new stade();
        $page="Stadiums Add";
        $form = $this->createFormBuilder($stade)
            ->add('stadeName', TextType::class,array('attr'=>array('placeholder'=>'Nom Stade' )))
            ->add('stadeOwner', TextType::class,array('attr'=>array('placeholder'=>'Proprietaire Stade' ) ))
            ->add('password', PasswordType::class,array('attr'=>array('placeholder'=>'Password' ) ))
            ->add('stadeDescription', TextareaType::class,array('attr'=>array('placeholder'=>'Description Stade'  )))
            ->add('stadePhone', IntegerType::class,array('attr'=>array('placeholder'=>'Telephone')))
            ->add('adresse', TextType::class,array('attr'=>array('placeholder'=>'Adresse' ) ))
            ->add('stadeLocation', ChoiceType::class,[
                'choices' => [
                    'Tunis' => 'Tunis',
                    'Ben Arous' => 'Ben Arous',
                    'Ariana' => 'Ariana',
                    'Manouba' => 'Manouba'
                ],'label' => false,
                ])
            ->add('superficie', IntegerType::class,array('attr'=>array('placeholder'=>'Superficie' ) ))
            ->add('supplements', TextType::class,array('attr'=>array('placeholder'=>'Supplements' ) ))
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
                $stade->setFeatured(false);
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
     * @Route("/stadium/{id}/cpanel/vorder/{idd}", name="vOrder")
     */
    public function verifyOrder(int $id,OrdersRepository $ordersrepo,Request $request,int $idd,StadeRepository $staderepo,UserPasswordEncoderInterface $encoder): Response
    {
            

            $this->denyAccessUnlessGranted("IS_ANONYMOUS");
            $stade = $staderepo->find($id);
            $form=$this->createFormBuilder()
                ->add("Password",PasswordType::class)
                ->add("Submit",SubmitType::class)
                ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
                if($encoded == $stade->getPassword()){
                    $this->page=$encoded;
                    $orderr = $ordersrepo->find($idd);
                    $orderr->setVerified(true);
                    $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->persist($orderr);
                            $entityManager->flush();
                    return $this->redirectToRoute('stadiumOrders',['id'=>$id]);
                }else{
                    $this->addFlash('failure', 'Wrong Password');
                }
            }
            $orders = $stade->getOrders();
            return $this->render('stade/orders.html.twig', [
                'page'=>$this->page,'stade'=>$stade,'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$orders,'form'=>$form->createView()
            ]);
    }
    /**
     * @Route("/stadium/{id}/cpanel/{idd}/dorder", name="dorder")
     */
    public function dOrder(int $idd,OrdersRepository $ordersrepo,Request $request,int $id,StadeRepository $staderepo,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
            $stade = $staderepo->find($id);
            $form=$this->createFormBuilder()
                ->add("Password",PasswordType::class)
                ->add("Submit",SubmitType::class)
                ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
                if($encoded == $stade->getPassword()){
                    $this->page=$encoded;
                    $orderr = $ordersrepo->find($idd);
                    $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->remove($orderr);
                            $entityManager->flush();
                    return $this->redirectToRoute('stadiumOrders',['id'=>$id]);
                }else{
                    $this->addFlash('failure', 'Wrong Password');
                }
            }
            $orders = $stade->getOrders();
            return $this->render('stade/orders.html.twig', [
                'page'=>$this->page,'stade'=>$stade,'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$orders,'form'=>$form->createView()
            ]);
    }
    /**
     * @Route("/stadium/{id}/cpanel/{idd}/dcomments", name="dstadcomment")
     */
    public function dComment(int $idd,CommentsRepository $commentsrepo,Request $request,int $id,StadeRepository $staderepo,UserPasswordEncoderInterface $encoder): Response
    {
        $this->denyAccessUnlessGranted("IS_ANONYMOUS");
            $stade = $staderepo->find($id);
            $form=$this->createFormBuilder()
                ->add("Password",PasswordType::class)
                ->add("Submit",SubmitType::class)
                ->getForm();
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                
                $encoded = $encoder->isPasswordValid($stade, $form->get('Password')->getData());
                if($encoded == $stade->getPassword()){
                    $this->page=$encoded;
                    $comment = $commentsrepo->find($idd);
                    $entityManager = $this->getDoctrine()->getManager();
                            $entityManager->remove($comment);
                            $entityManager->flush();
                    return $this->redirectToRoute('stadcomments',['id'=>$id]);
                }else{
                    $this->addFlash('failure', 'Wrong Password');
                }
            }
            $orders = $stade->getOrders();
            return $this->render('stade/orders.html.twig', [
                'page'=>$this->page,'stade'=>$stade,'controller_name' => 'CpanelController','logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','orders'=>$orders,'form'=>$form->createView()
            ]);
    }
    /**
     * @Route("/stadiums/{id}", name="stadiumsShow")
     */
    public function show(ReviewsRepository $ratingrepo,int $id,Request $request,Request $request2,StadeRepository $staderepo,CommentsRepository $commentsrepo,UserRepository $usersrepo,OrdersRepository $ordersrepo) : Response {
        
        $stade = $staderepo
            ->find($id);
            if (!$stade) {
                throw $this->createNotFoundException(
                    'No stade found for id '.$id
                );
            }
            $page = "Stade";
            $comment = new Comments();
            $form = $this->createFormBuilder($comment)
                    ->add('content',TextType::Class,['attr'=>['placeholder'=>'Comment'],'label'=>false])
                    ->add('Submit',SubmitType::Class,['label'=>"❯"])
                    ->getForm()
            ;
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid() && $request->request->has('form')) {
                
                $comment->setStadeId($id);
                $user = $this->getUser();
                $comment->setUser($user);
                $comment->setDate(new \DateTime('now'));
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($comment);
                $entityManager->flush();
                
                return $this->redirectToRoute('stadiumsShow',['id'=>$id]);
            }
            $comments = $commentsrepo->findBy(['stadeId'=>$id]);
            $order=new Orders();
            $orderform = $this->createFormBuilder($order)
            ->add("startDate",DateTimeType::Class,['attr'=>['placeholder'=>'Comment'],'label'=>'Start Date' ])
            ->add("endDate",DateTimeType::Class,['attr'=>['placeholder'=>'Comment'],'label'=>'End Date'])
            ->add("submitt",SubmitType::Class,['attr'=>['placeholder'=>'Comment'],'label'=>'Submit'])
            ->getForm();
            $orderform->handleRequest($request);
            if ($orderform->isSubmitted()) {
                if($orderform->isValid()){
                $exist=$ordersrepo->getByDate($orderform->get('startDate')->getData(),$orderform->get('endDate')->getData());
                if($exist){
                    $this->addFlash('failure', 'Already Reserved');
                }else{
                    $staade=$staderepo->find($id);
                $user = $this->getUser();
                $order->setUser($user);
                $order->setVerified(false);
                $order->setStade($staade);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($order);
                $entityManager->flush();
                return $this->redirectToRoute('stadiumsShow',['id'=>$id]);
                }
                }else{
                    $this->addFlash('failure', 'Reservation Failed - Check Dates');
                }
                
            }
            
                $ordersss = $ordersrepo->findBy(['Stade'=>$id,'verified'=>true]);
            $rdv = [];
            foreach($ordersss as $orderss){
                $rdv[]= [
                    'id'=> $orderss->getId(),
                    'start'=> $orderss->getStartDate()->format('Y-m-d H:i:s'),
                    'end'=> $orderss->getEndDate()->format('Y-m-d H:i:s'),
                    'title'=>$orderss->getUser()->getUserName(),
                    'backgroundColor'=>'lightgreen'

                ];
            };
            $page="Stadiums";
            $ratings = $ratingrepo->findBy(['stade'=>$stade]);
            $rate = $ratingrepo->findOneBy(['user'=>$this->getUser(),'stade'=>$stade]);
            $exists = false;
            if($rate){
                $exists = true;
            }
            $average = 0;
            $i=0;
            foreach($ratings as $rating){
                $average += $rating->getRating();
                $i++;
            }
            if($i!=0){
                $average = $average/$i;
            }
            $date=json_encode($rdv);
        return $this->render('stade/stadium.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','average'=>$average,'exists'=>$exists, 'stade' => $stade,'form'=>$form->createView(),'comments'=>$comments,'orderForm'=>$orderform->createView(),'data'=>compact('date')
        ]);
    }
    /**
     * @Route("/stadiums/{id}/order", name="stadiumOrder" )
     */
    public function order(int $id,Request $request,StadeRepository $staderepo,UserRepository $usersrepo,OrdersRepository $ordersrepo) : Response {
        $order=new Orders();
        $stade = $staderepo
            ->find($id);
            if (!$stade) {
                throw $this->createNotFoundException(
                    'No stade found for id '.$id
                );
            }
        $start = $request->query->get('startDate');
        $end = $request->query->get('endDate');
        if(
         (isset($start) && !empty($start)) &&
         (isset($end) && !empty($end))
        ){ 
            $startDate = new \DateTime( $start );
            $endDate = new \DateTime( $end );
            $dateNow=new \DateTime('now');
            $exist=$ordersrepo->getByDate($startDate,$endDate,$stade);
                if($exist){
                    $this->addFlash('failure', 'Already Reserved');
                    return $this->redirectToRoute('stadiumOrder',['id'=>$id]);
                }else{
                    $this->addFlash('failure', 'Reservation Done');
                    $staade=$staderepo->find($id);
                $user = $this->getUser();
                if($startDate<$dateNow){
                    $this->addFlash('failure', 'Choose a later date');
                    return $this->redirectToRoute('stadiumOrder',['id'=>$id]);
                }
                $order->setEndDate($endDate);
                $order->setStartDate($startDate);
                $order->setUser($this->getUser());
                $order->setVerified(false);
                $order->setStade($stade);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($order);
                $entityManager->flush();
                
                return $this->redirectToRoute('stadiumsShow',['id'=>$id]);
                }
        }else{
            
        }
        $ordersss = $ordersrepo->findBy(['Stade'=>$id,'verified'=>true]);
            $rdv = [];
            foreach($ordersss as $orderss){
                $rdv[]= [
                    'id'=> $orderss->getId(),
                    'start'=> $orderss->getStartDate()->format('Y-m-d H:i:s'),
                    'end'=> $orderss->getEndDate()->format('Y-m-d H:i:s'),
                    'title'=>$orderss->getUser()->getUserName(),
                    'backgroundColor'=>'lightgreen'

                ];
            };
            $page="Stadiums";
            $date=json_encode($rdv);
        return $this->render('stade/calendar.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','data'=>compact('date')
        ]);
    }
    /**
     * @Route("/stadiums/{id}/rating", name="stadiumRating" )
     */
    public function rating(int $id,Request $request,ReviewsRepository $ratingrepo,StadeRepository $staderepo,UserRepository $usersrepo,OrdersRepository $ordersrepo) : Response {
        $this->denyAccessUnlessGranted("ROLE_USER");
        $stade = $staderepo->find($id);
        $user = $this->getUser();
        $rate = $ratingrepo->findOneBy(['user'=>$this->getUser(),'stade'=>$stade]);
            
            if($rate){
                return $this->redirectToRoute('stadiumsShow',['id'=>$id]);
            }
        $review = new Reviews;
        $review->setUser($user);
        
        $review->setStade($stade);
        $rating = $request->query->get('rating');
        if($rating>=1 && $rating<=5){
            $review->setRating($rating);
            $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($review);
                $entityManager->flush();
                $this->addFlash('failure', 'Rating Added');
        }
        
        return $this->redirectToRoute('stadiumsShow',['id'=>$id]);
    }
    //  Calendar System : 
    /*
    public function Calendar(Request $request,OrdersRepository $ordersrepo): Response
    {
        $orders = $ordersrepo->findAll();
        $rdv = [];
        foreach($orders as $order){
            $rdv[]= [
                'id'=> $order->getId(),
                'start'=> $order->getStartDate()->format('Y-m-d H:i:s'),
                'end'=> $order->getEndDate()->format('Y-m-d H:i:s'),
                'title'=>$order->getUser()->getUserName(),
                'backgroundColor'=>'lightgreen'

            ];
        };
        $page="Stadiums";
        $date=json_encode($rdv);
        return $this->render('stade/calendar.html.twig',[
            'page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg','data'=>compact('date')
        ]);
    }*/
}
