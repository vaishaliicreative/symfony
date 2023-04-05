<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name',TextType::class,)
            ->add('email',EmailType::class)
            ->add('password',RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'The password fields must match.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password']
            ])
            ->add('phone_number')
            ->add('gender',ChoiceType::class,[
                'choices' =>[
                    'Male' => 'Male',
                    'Female' => 'Female',
                ],
                'multiple' => false,
                'expanded' => true,
                'data' => 'Male'
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
                    'expanded' => true,]
            )
            ->add('roles',ChoiceType::class,[
                'choices' =>[
                    'Admin' => 'ROLE_ADMIN',
                    'User' => 'ROLE_USER',
                ],
                'multiple' => true,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
