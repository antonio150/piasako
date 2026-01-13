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
use Symfony\Component\Form\Extension\Core\Type\ColorType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

class TachesTypeForm extends AbstractType
{
    private DynamicEntityManagerProvider $dynamicEntityManagerProvider;

    public function __construct(DynamicEntityManagerProvider $dynamicEntityManagerProvider)
    {
        $this->dynamicEntityManagerProvider = $dynamicEntityManagerProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ttpLibelle', TextType::class, [
                'label' => 'Libellé',
                'required' => true,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le libellé est requis.']),
                    new Length([
                        'max' => 50,
                        'maxMessage' => 'Le libellé ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('ttpCouleur', ColorType::class, [
                'label' => 'Couleur (Hex Code)',
                'required' => true,
                'attr' => [
                    'class' => 'form-control form-control-color',
                    'disabled' => $options['disabled'],
                ],
                'constraints' => [
                    new NotBlank(['message' => 'La couleur est requise.']),
                ],
            ])
            ->add('ttpcomptabiliser', CheckboxType::class, [
                'label' => 'Comptabiliser',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input custom-form-check-input',
                    'disabled' => $options['disabled'],
                ],
            ])
            ->add('ttpplanifiable', CheckboxType::class, [
                'label' => 'Planifiable',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input custom-form-check-input',
                    'disabled' => $options['disabled'],
                ],
            ])
            ->add('ttpNbTravailleurRequis', NumberType::class, [
                'label' => 'Nombre de travailleurs requis',
                'required' => false,
                'attr' => [
                    'class' => 'form-control custom-form-control',
                    'disabled' => $options['disabled'],
                ],
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TachesType::class,
            'disabled' => false,
        ]);

        $resolver->setAllowedTypes('disabled', 'bool');
    }
}