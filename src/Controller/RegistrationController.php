<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Component\Mime\Email;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');
        $user = new User();
        $form = $this->createFormBuilder($user)
                ->add('userName', TextType::class,['label'=>false])
                ->add('userPass', PasswordType::class,['label'=>false])
                ->add('confirmPass',PasswordType::class,['label'=>false])
                ->add('userEmail',EmailType::class,['label'=>false])
                ->add('userPhone')
                ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()){
            // encode the plain password
            $user->setUserPass(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('userPass')->getData()
                )
            );
            $user->setIsVerified(true);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            /*
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@takwira.com', 'No Reply'))
                    ->to($user->getUserEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );*/
                    $this->addFlash('failure', 'A verification email has been sent to your email');
                    
                    return $this->redirectToRoute('login');
                }
        }
        $page = "Register";
        return $this->render('home/register.html.twig', ['page'=>$page,'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg',
            'form' => $form->createView(),
        ]);
    }
}