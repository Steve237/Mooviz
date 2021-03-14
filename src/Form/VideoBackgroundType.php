<?php

namespace App\Form;

use App\Form\UploadType;
use App\Entity\Videobackground;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class VideoBackgroundType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('videotitle', TextType::class)
            ->add('videodescription', TextareaType::class)
            ->add('parutiondate')
            ->add('videoduration')
            ->add('videolink', FileType::class, array('data_class' => null))
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
