<?php

namespace App\Form;

use App\Entity\Paiement;
use App\Entity\Planning;
use App\Entity\Utilisateur;
use Doctrine\DBAL\Types\DateTimeType;
use Doctrine\DBAL\Types\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Form\FormTypeInterface;


class PaiementBackType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('statut', ChoiceType::class,
                [
                    'choices'=>['En Cours'=>'En Cours',
                        'Effectué'=>'Effectué',
                        'Annulé'=>'Annulé']

                ])
            ->add('dateCreation', \Symfony\Component\Form\Extension\Core\Type\DateType::class,[
                'widget'=> 'single_text'
            ])
            ->add('datePaiement',\Symfony\Component\Form\Extension\Core\Type\DateType::class,[
                'widget'=> 'single_text'
            ])
            ->add('typePaiement' , ChoiceType::class,[
                'choices'=>[ 'En Ligne' =>'En Ligne',
                    'Cash'=>'Cash'],

            ])
            ->add('owner',EntityType::class,
                [
                    'class'=>Utilisateur::class,
                    'choice_label'=> 'nom'
                ])
            ->add('client', EntityType::class,
                [
                    'class'=>Utilisateur::class,
                    'choice_label'=> 'nom'
                ])
            ->add('planning',EntityType::class,
                [
                    'class'=>Planning::class,
                    'choice_label'=> 'id'
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Paiement::class,
        ]);
    }
}
