<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{

    #[Route('/registration', name: 'registration')]
    public function index(Request $request,UserPasswordHasherInterface $passwordEncoder,EntityManagerInterface $entityManager): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

//        echo "<pre>";
//        print_r($request);
//        exit;
        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new users password
            $user->setPassword($passwordEncoder->hashPassword($user, $user->getPassword()));

            // Set their role
//            $user->setRoles(['ROLE_ADMIN']);
            $role = $form->get('roles')->getData();
            $user->setRoles($role);
            $profileFile = $form->get('profile_image')->getData();

            if ($profileFile) {
                $originalFilename = pathinfo($profileFile->getClientOriginalName(), PATHINFO_FILENAME);
                $fileName = $originalFilename.md5(uniqid()) . '.' . $profileFile->guessExtension();
                try {
                    $profileFile->move($this->getParameter('profile_directory'), $fileName);
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    echo $e->getMessage(); die();
                }
                $user->setProfileImage($fileName);
            }
            // Save
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_login');
        }
        return $this->render('registration/index.html.twig', [
            'controller_name' => 'RegistrationController',
            'form' => $form,
        ]);
    }
}
