<?php 

namespace App\Form;

use App\Entity\Dynamic\Taches;
use App\Entity\Dynamic\TachesPriorites;
use App\Entity\Dynamic\TachesType;
use App\Entity\Main\Site;
use App\Service\DynamicEntityManagerProvider;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class TachesForm extends AbstractType
{
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;

    public function __construct(DynamicEntityManagerProvider $dynamicEntityManagerProvider)
    {
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('tacNom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Nom est requise.']),
                   
                ],
            ])
            ->add('tacDescription', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => "Déscription est requise."]),
                    
                ],
            ])
            ->add('tacNbtravailleurrequis', IntegerType::class, [
                'label' => 'Travailleur réquis',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le travailleur est requis.']),
                    
                ],
            ])
            ->add('tacDureeestimatif', IntegerType::class, [
                'label' => 'Durée estimative',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => "L'adresse email est requise."]),
                ],
            ])
            ->add('datePrevision', DateTimeType::class, [
                'label' => 'Date prévision',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le date est requis.']),
                   
                ],
            ])
            ->add('idTachesPriorites', EntityType::class, [
                'class' => TachesPriorites::class,
                'choice_label' => 'Priorité',
                'placeholder' => 'Sélectionnez un priorité',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    $dynamicEm = $this->dynamicEntityManagerProvider->getEntityManager();
                    $repo = $dynamicEm->getRepository(TachesPriorites::class);
                    return $repo->createQueryBuilder('p')
                        ->orderBy('p.tptNiveau', 'ASC');
                },
                'attr' => [
                    'class' => 'form-control custom-form-control'
                ],
            ])
            ->add('tacTauxHoraire', NumberType::class, [
                'label' => 'Taux horaire',
                'required' => true,
                'scale' => 2, // nombre de décimales (ex : 2 pour 12.50)
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'step' => '0.01', // permet les décimales dans le navigateur
                ],
            ])
            ->add('tacBudget', NumberType::class, [
                'label' => 'Budget',
                'required' => true,
                'scale' => 2, // nombre de décimales (ex : 2 pour 12.50)
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'step' => '0.01', // permet les décimales dans le navigateur
                ],
            ])
            ->add('idTachesType', EntityType::class, [
                'class' => TachesType::class,
                'choice_label' => 'Type',
                'placeholder' => 'Sélectionnez un type',
                'required' => true,
                'query_builder' => function (EntityRepository $er) {
                    $dynamicEm = $this->dynamicEntityManagerProvider->getEntityManager();
                    $repo = $dynamicEm->getRepository(TachesType::class);
                    return $repo->createQueryBuilder('t')
                        ->orderBy('t.ttpLibelle', 'ASC');
                },
                'attr' => [
                    'class' => 'form-control custom-form-control'
                ],
            ])

            ->add('periodic', CheckboxType::class, [
                'label' => 'Periodic',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input bg-primary border border-primary',
                    'role' => 'switch',
                    'disabled' => $options['disabled'],
                ],
            ])

            ->add('joursExecution', ChoiceType::class, [
                'label' => 'Jours d’exécution',
                'multiple' => true,
                'expanded' => false, // false = select multiple, true = cases à cocher
                'choices' => [
                    'Dimanche' => 0,
                    'Lundi' => 1,
                    'Mardi' => 2,
                    'Mercredi' => 3,
                    'Jeudi' => 4,
                    'Vendredi' => 5,
                    'Samedi' => 6,
                ],
                'required' => false,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
            ])

            ->add('tacShiftJour', CheckboxType::class, [
                'label' => 'Shift jour',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input bg-primary border border-primary',
                    'role' => 'switch',
                    'disabled' => $options['disabled'],
                ],
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
            'data_class' => Taches::class,
            'disabled' => false,
        ]);

        $resolver->setAllowedTypes('disabled', 'bool');
    }
}