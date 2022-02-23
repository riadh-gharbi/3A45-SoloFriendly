<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;


//Ici on va créer le formulaire pour l'ajout d'une nouvelle facture.
//Le owner, client et planning est supposé connu
//Le prix est issue du planning
//La date de création est générée automatiquement avec l'ajout submit
//La date de paiement va etre null jusqu'a confirmation
//statut va etre automatiquement changé en "onHold"
//Le seul champs a "remplir" est le type de paiement
class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('prix')
            //->add('statut')
            //->add('date_creation')
            //->add('date_paiement')
            ->add('typePaiement', ChoiceType::class,[
                'choices'=>[ 'En Ligne' =>'En Ligne',
                    'Cash'=>'Cash'],

            ])
            //->add('owner')
            //->add('client')
            //->add('planning')
        ;



    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
