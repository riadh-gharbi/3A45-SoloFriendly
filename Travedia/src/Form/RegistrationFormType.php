<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class,[
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        [
                            'min'=> 5,
                            'max'=>30,
                            'minMessage'=>'Le Nom doit contenir au moins 5 carcatères',
                            'maxMessage'=>'Le Nom doit contenir au plus 30 carcatères'
                        ]
                    )
                ]
            ])
            ->add('prenom', TextType::class,[
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        [
                            'min'=> 5,
                            'max'=>30,
                            'minMessage'=>'Le Prenom doit contenir au moins 5 carcatères',
                            'maxMessage'=>'Le Prenom doit contenir au plus 30 carcatères'
                        ]
                    )
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Email([
                        'message' => 'Vous devez entrer une adresse mail valide'
                    ]),
                    new NotBlank(),
                    new Length(
                        [
                            'min'=> 5,
                            'max'=>30,
                            'minMessage'=>'Le mot de passe doit contenir au moins 5 carcatères',
                            'maxMessage'=>'Le mot de passe doit contenir au plus 30 carcatères'
                        ]
                    )
                ]
            ])
            ->add('plainPassword', PasswordType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'label' => 'Mot de passe',
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(),
                    new Length(
                        [
                            'min'=> 5,
                            'max'=>30,
                            'minMessage'=>'Le mot de passe doit contenir au moins 5 carcatères',
                            'maxMessage'=>'Le mot de passe doit contenir au plus 30 carcatères'
                        ]
                    )
                ]

            ])
            ->add('langue', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Arabe' => 'Arabe',
                    'Français' => 'Français',
                    'Anglais' => 'Anglais',
                ]
            ])->add('roles', ChoiceType::class, [
                'multiple' => false,
                'expanded' => false,
                'choices' => [
                    'Guide' => 'Guide',
                    'Voyageur' => 'Voyageur',
                ]
            ])
            ->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray) {
                    return count($rolesAsArray) ? $rolesAsArray[0] : null;
                },
                function ($rolesAsString) {
                    return [$rolesAsString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
