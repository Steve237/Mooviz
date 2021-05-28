<?php

namespace App\Form;

use App\Entity\Videobackground;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


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
            
            ->add('videolink', UrlType::class, ['data_class' => null])
            
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
