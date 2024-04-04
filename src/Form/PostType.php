<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data', FileType::class, [
                'label' => "Images",
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*'],
                'multiple' => true,
            ])
            ->add('titre', TextType::class, [
                'label' => "Titre",
                'attr' => ['placeholder' => "Titre du post"],
                'required' => true,
            ])
            ->add('description', TextType::class, [
                'label' => "Description",
                'attr' => ['placeholder' => "Description du post"],
                'required' => true,
            ])
            ->add('date')
            ->add('temps_retard')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
