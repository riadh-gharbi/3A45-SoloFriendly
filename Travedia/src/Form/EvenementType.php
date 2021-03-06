<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Evenement;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class EvenementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',null, ['label' => false])
            ->add('description',null, ['label' => false])
            ->add('datedeb' ,DateType::class, [
                'widget' => 'single_text',
                'constraints'=>[
                    new NotBlank()
                ],
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',])
            ->add('datefin' ,DateType::class, [
                'widget' => 'single_text',
                'constraints'=>[
                    new NotBlank()
                ],
                // this is actually the default format for single_text
                'format' => 'yyyy-MM-dd',])
            ->add('picture', FileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'image/*',
                        ],
                        'mimeTypesMessage' => 'Verify your image type',
                    ])
                ],
            ])
            //->add('utilisateur')
            ->add('categorie',EntityType::class,['class'=>Categorie::class,'choice_label'=>'nom'])
            ->add('Ajouter', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenement::class,
        ]);
    }
}
