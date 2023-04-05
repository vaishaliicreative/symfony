<?php

namespace App\Controller;

use App\Form\UserType;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
class ProfileController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/profile', name: 'app_profile')]
    public function index(Request $request): Response
    {

        $user = $this->security->getUser();
        $roles = $user->getRoles();
        $session = $request->getSession();
        $session->set('role', $roles);

        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user'     => $user,
        ]);
    }

    #[Route('/profile/{id}/edit',name:'app_profile_edit',methods: ['GET','POST'])]
    public  function  editProfile(Request $request,User $user,EntityManagerInterface $entityManager){
        $form = $this->createFormBuilder($user)
            ->add('name',TextType::class)
            ->add('phone_number')
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

        if ($form->isSubmitted()) {
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
//            $entityManager->persist($user);
//            echo "<pre>";
//            print_r($user);
//            exit;
            $entityManager->flush();

            return $this->redirectToRoute('app_profile');
        }
        return $this->render('profile/editprofile.html.twig', [
            'controller_name' => 'ProfileController',
            'user'     => $user,
            'form'     => $form
        ]);
    }
}
