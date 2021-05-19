<?php

namespace App\Form;

use App\Entity\Videobackground;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TimeType;


class VideoBackgroundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videotitle', TextType::class)
            ->add('videodescription', TextareaType::class)
            ->add('parutiondate')
            ->add('videoduration', TimeType::class, [
                'placeholder' => ['hour' => 'Hour', 'minute' => 'Minute', 'second' => 'Second']
                
                ])
            
            ->add('videolink', FileType::class, [
                'data_class' => null,
                
                "attr" => [
                    
                    "accept" => "video/mp4"
                ],
                
                "constraints" => [
                    new File([
                        'maxSize' => '1000M',
                        'mimeTypes' => [
                            'video/mp4'
                        ],
                        
                        'mimeTypesMessage' => 'Nous acceptons uniquement les vidéos au format mp4.'
                    ])
                ]
            ]
            )
            
            ->add('category')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Videobackground::class,
        ]);
    }
}
