<?php

namespace App\Form;

use App\Entity\Videos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;



class VideoType extends AbstractType
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
                        'maxMessage' => "Votre titre doit contenir au moins 35 caractères"
                    ])
                ]
            ])
            ->add('videodescription', TextareaType::class, [
                "label" => "Ajoutez une description.",
            ])
            ->add('videoimage', FileType::class, [
                "data_class" => null,
                "label" => "Ajoutez une image.",
                "constraints" => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                            'image/jpg',
                            'image/jpeg',
                            'image/png'
                        ],
                        'mimeTypesMessage' => 'Nous acceptons uniquement image au format jpg, jpeg, ou png.'
                    ])
                ]
            ])

            ->add('videolink', FileType::class, [
                "data_class" => null,
                "label" => "Ajoutez une vidéo.",
                "constraints" => [
                    new File([
                        'maxSize' => '1000M',
                        'mimeTypes' => [
                            'video/mp4'
                        ],
                        'mimeTypesMessage' => 'Nous acceptons uniquement les vidéos au format mp4.'
                    ])
                ]
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
