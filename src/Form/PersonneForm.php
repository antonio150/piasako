<?php

namespace App\Form;

use App\Entity\Dynamic\Personne;
use App\Entity\Dynamic\Profil;
use App\Entity\Main\Site;
use App\Enum\Sex;
use App\Service\DynamicEntityManagerProvider;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class PersonneForm extends AbstractType
{
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;

    public function __construct(DynamicEntityManagerProvider $dynamicEntityManagerProvider)
    {
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // préparation des choix pour l'enum Sex
        $sexChoices = [];
        foreach (Sex::cases() as $case) {
            // libellé lisible (vous pouvez remplacer par $case->name ou $case->value selon besoins)
            $label = ucfirst(strtolower($case->name));
            $sexChoices[$label] = $case;
        }

        $builder
            ->add('persPrenom', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [
                    new NotBlank(['message' => 'Le prénom est requis.']),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('persNom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom est requis.']),
                    new Length(['max' => 100]),
                ],
            ])
            ->add('persMail', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [
                    new Email(['message' => 'Email invalide.']),
                    new Length(['max' => 180]),
                ],
            ])
            ->add('persContact', TelType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [
                    new Length(['max' => 30]),
                ],
            ])
           
            // nouveaux champs demandés
            ->add('persNumBadge', TextType::class, [
                'label' => 'Numéro badge',
                'required' => false,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [ new Length(['max' => 50]) ],
            ])
            ->add('persSex', ChoiceType::class, [
                'label' => 'Sexe',
                'required' => false,
                'choices' => $sexChoices, // valeurs = instances de l'enum
                'placeholder' => 'Sélectionnez',
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
            ])
            ->add('idProfil', EntityType::class, [
                'class' => Profil::class,
                'choice_label' => function ($profil) { return (string) $profil; }, // utilise __toString si présent
                'placeholder' => 'Sélectionnez un profil',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    $dynamicEm = $this->dynamicEntityManagerProvider->getEntityManager();
                    $repo = $dynamicEm->getRepository(Profil::class);
                    return $repo->createQueryBuilder('p')->orderBy('p.idProfil', 'ASC');
                },
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
            ])
            ->add('persNumCIN', TextType::class, [
                'label' => 'Numéro CIN',
                'required' => false,
                'attr' => ['class' => 'form-control custom-form-control', 'disabled' => $options['disabled']],
                'constraints' => [ new Length(['max' => 50]) ],
            ])

            ->add('estActif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input bg-primary border border-primary',
                    'role' => 'switch',
                    'disabled' => $options['disabled'],
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
            'disabled' => false,
        ]);

        $resolver->setAllowedTypes('disabled', 'bool');
    }
}

