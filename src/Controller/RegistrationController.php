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
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder,MailerInterface $mailer): Response
    {
        $this->denyAccessUnlessGranted('IS_ANONYMOUS');
        $user = new User();
        $form = $this->createFormBuilder($user)
                ->add('userName')
                ->add('userPass', PasswordType::class)
                ->add('confirmPass',PasswordType::class)
                ->add('userEmail',EmailType::class)
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
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('no-reply@takwira.com', 'No Reply'))
                    ->to($user->getUserEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
                    $message = (new Email())
                ->from('send@example.com')
                ->to($user->getUserEmail())
                ->html(
                    $this->renderView(
                        // templates/emails/registration.html.twig
                        'home/index.html.twig',
                        ['page'=>'home','logo'=>'assets/logo.png','menu'=>'assets/menu.svg']
                    ),
                    'text/html'
                )
                
            ;
            $mailer->send($message);
                    $this->addFlash('failure', 'A verification email has been sent to your email');
                    return $this->redirectToRoute('login');
                }else{
                    $this->addFlash('failure', 'Username or Email already used');
                }
        }
        $page = "Register";
        return $this->render('home/register.html.twig', ['page'=>$user->getUserPass(),'logo'=>'assets/loogo.png','menu'=>'assets/menu2.svg',
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, UserRepository $userRepository): Response
    {
        $id = $request->get('id');

        if (null === $id) {
            return $this->redirectToRoute('app_register');
        }

        $user = $userRepository->find($id);

        if (null === $user) {
            return $this->redirectToRoute('app_register');
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('login');
    }
}
