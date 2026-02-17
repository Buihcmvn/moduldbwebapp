<?php
declare(strict_types=1);

namespace App\Controller;


use App\Entity\User;
use App\Form\SignupType;
use App\Form\LoginType;
use AllowDynamicProperties;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



#[AllowDynamicProperties] class LoginController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $userPasswordHasher,
        AuthenticationUtils $authenticationUtils
    )
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->authenticationUtils = $authenticationUtils;
    }

    #[Route('/signup', name: 'signup')]
    public function signup(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() )
        {

            // dd([
            //     'submitted' => $form->isSubmitted(),
            //     'valid' => $form->isValid(),
            //     'data' => $form->getData(),
            //     'request_all' => $request->request->all()
            // ]);

            // Hier können Sie das Passwort hashen, bevor Sie den Benutzer speichern
            $hashedPassword = password_hash($user->getPassword(), PASSWORD_BCRYPT);
            $user->setPassword($hashedPassword);

            $this->entityManager->persist($user);
            $this->entityManager->flush(); // Hier wird die ID automatisch generiert

            // Redirect oder andere Logik nach erfolgreichem Signup
            return $this->redirectToRoute('login'); // Ändern Sie dies entsprechend
        }

        return $this->render('Login/register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/login', name: 'login')]
    public function login(Request $request): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('home');
        }
        // Create the login form
        $form = $this->createForm(LoginType::class);
        // Handle the request
        $form->handleRequest($request);

        $errors = $this->authenticationUtils->getLastAuthenticationError();
        // Authentifizierung fehlgeschlagen
        ($errors)? $this->addFlash('error', $errors->getMessage()." ! ") :'';
        return $this->render('Login/login.html.twig', [
            'form'          => $form,
            'errors'         => $errors,
        ]);
    }


    #[Route('/login_check', name: 'login_check')]
    public function check(): void
    {
//        dd('redirect to home');
        // This method is never executed. Symfony will intercept the request and authenticate the user.
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        $session = $this->container->get('session'); // Hoặc sử dụng RequestStack (xem bên dưới)

        // Invalidate the session
        $session->invalidate();
        // Symfony handles logout automatically
        return $this->redirectToRoute('login');
    }


}
