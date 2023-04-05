<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Form\UserType;

class UserController extends AbstractController
{
    #[Route('/user', name: 'app_user')]
    public function index(EntityManagerInterface $entityManager): Response
    {
//        $users = $entityManager->getRepository(User::class)->findAll();
        $users = $entityManager->getRepository(User::class)->findUsersByRole('ROLE_USER');
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
            'users' => $users
        ]);
    }

    #[Route('/user/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
//        $student = $entityManager->getRepository(Student::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException(
                'No product found for id '
            );
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    #[Route('/user/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder($user)
            ->add('name',TextType::class,)
            ->add('email',EmailType::class,)
            ->add('phone_number',null,)
            ->add('gender',ChoiceType::class,[
                'choices' =>[
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'multiple' => false,
                'expanded' => true,

            ])
            ->add('profile_image',FileType::class,[
                    'mapped' => false,
                    'required' => false,
                ]
            )
            ->add('hobbies',ChoiceType::class,[
                    'choices' =>[
                        'Reading' => 'Reading',
                        'Music' => 'Music',
                        'Dancing' => 'Dancing',
                        'Shopping' => 'Shopping',
                        'Singing' => 'Singing'
                    ],
                    'multiple' => true,
                    'expanded' => true,
                ]
            )
            ->add('save', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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
            $entityManager->flush();

            return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    #[Route('/user/delete/{id}', name: 'app_userDelete', methods: ['GET'])]
    public function deleteUser(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
//        $id = $request->get('id');
//        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
//        }

        return $this->redirectToRoute('app_user', [], Response::HTTP_SEE_OTHER);
    }


}
