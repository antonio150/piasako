<?php

namespace App\Form;

use App\Entity\Main\SuperUser;
use App\Entity\Profil;
use App\Entity\Site;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SuperUserForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isEdit = $options['is_edit'] ?? false;

        $passwordConstraints = [
            new Length([
                'min' => 8,
                'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
            ]),
            new Regex([
                'pattern' => '/[A-Z]/',
                'message' => 'Le mot de passe doit contenir au moins une lettre majuscule.',
            ]),
            new Regex([
                'pattern' => '/[a-z]/',
                'message' => 'Le mot de passe doit contenir au moins une lettre minuscule.',
            ]),
            new Regex([
                'pattern' => '/\d/',
                'message' => 'Le mot de passe doit contenir au moins un chiffre.',
            ]),
            new Regex([
                'pattern' => '/[\W_]/',
                'message' => 'Le mot de passe doit contenir au moins un caractère spécial.',
            ]),
        ];

        // Add NotBlank constraint only for creation (not edit)
        if (!$isEdit) {
            $passwordConstraints[] = new NotBlank([
                'message' => 'Le mot de passe ne peut pas être vide.',
            ]);
        }

        $builder
          
            ->add('email', EmailType::class, [
                'required' => true
            ])
         
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => $isEdit ? false : $options['password'],
                'mapped' => false,
                'constraints' => $passwordConstraints,
            ])
            
            ->add('estActif', CheckboxType::class, [
                'required' => false,
                'label' => 'Actif'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuperUser::class,
            'profiles' => [],
            'password' => false,
            'is_edit' => false,
        ]);
    }
}