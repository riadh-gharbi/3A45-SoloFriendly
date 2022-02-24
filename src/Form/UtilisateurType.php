<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cin', IntegerType::Class)

            ->add('nom', TextType::Class)
            ->add('prenom', TextType::Class)
            ->add('adresse', TextType::Class)
            ->add('numTel', IntegerType::Class)
           ->add('email', EmailType::Class)
           ->add('MotDePasse', PasswordType::Class)
            ->add('role', ChoiceType::Class,
                [  'choices' => [
                    'voyageur' =>"voyageur",
                    'Admin' =>"admin",
                    'Guide' =>"guide",

                    ]])

         ->add('langue', ChoiceType::Class,
                [  'choices' => [
                    'Arabe' =>"ar",
                    'Français' =>"fr",
                   'Anglais' =>"en",

                ]]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
