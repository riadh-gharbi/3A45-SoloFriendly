<?php

namespace App\Form;

use App\Entity\Destination;
use App\Entity\Evenement;
use App\Entity\Planning;
use App\Entity\Utilisateur;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanningType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date_depart')
            ->add('date_fin')
            ->add('prix')
            ->add('type_plan',ChoiceType::class,[
                'choices'=>[
                    'Premium'=>'Premium',
                    'Standard'=>'Standard',
                ],
            ])
            ->add('description')
            ->add('utilisateur',EntityType::class,['class'=>Utilisateur::class,'choice_label'=>'nom'])
            ->add('evenements',EntityType::class,['class'=>Evenement::class,'choice_label'=>'nom','expanded'=>'true','multiple'=>'true'])

            ->add('destinations',EntityType::class,['class'=>Destination::class,'choice_label'=>'nom','expanded'=>'true','multiple'=>'true'])
           // ->add('actualite')
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planning::class,
        ]);
    }
}
