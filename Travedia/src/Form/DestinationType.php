<?php

namespace App\Form;

use App\Entity\Destination;
use App\Entity\Region;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

//use Symfony\Component\Form\Extension\Core\Type\EntityType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;



class DestinationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description',TextareaType::class)
            // ->add('image', FileType::class, [
            //     'label'=>'image',
            //     'mapped' => false,
            //     'multiple' => true
            //     ])
                ->add('image', FileType::class, [
                    'mapped' => false,
                    'required' => false,           
                    'constraints' => [
                        new File([
                            'mimeTypes' => [
                                'image/*',
                            ],
                            'mimeTypesMessage' => 'Verify your image type',
                        ])
                    ],
                ])
                ->add('region',EntityType::class,['class'=>Region::class,'choice_label'=>'nom'])
                     
         //   ->add('evaluation')
         //   ->add('region')
            // ->add('region', ChoiceType::class, [
            //     'choices' => [
            //         'plages' => 'Plage',
            //         'Urbain' => 'Urbain',
            //         'Montagne' => 'Montagne',
            //         'Desert' => 'Desert',
            //     ],
            //     'expanded'  => false, // liste déroulante
            //     'multiple'  => false, // choix multiple
            // ])
           // ->add('utilisateur')
          //  ->add('evenement')
           // ->add('planning')
            ->add('Create', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Destination::class,
        ]);
    }
}
