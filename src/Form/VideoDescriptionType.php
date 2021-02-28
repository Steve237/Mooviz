<?php

namespace App\Form;

use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;


class VideoDescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videotitle', TextType::class, [
                "label" => "Titre de la vidéo.",
                "constraints" => [
                    new Length([
                        'min' => 2,
                        'max' => 35,
                        'minMessage' => "Votre titre doit contenir au moins 2 caractères",
                        'maxMessage'=> "Votre titre doit contenir au moins 35 caractères"
                    ])

                ]
            ])
            ->add('videodescription', TextareaType::class, [
                "label" => "Description de la vidéo."
            ])

            ->add('category')
        ;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Videos::class,
        ]);
    }
}
