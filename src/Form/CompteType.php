<?php

namespace App\Form;

use App\Entity\Compte;
use App\Entity\Etablissement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CompteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('data', FileType::class, [
                'label' => 'Photo de profil',
                'mapped' => false,
                'required' => false,
                'attr' => ['accept' => 'image/*']
            ])
            ->add('etablissement_id', EntityType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Etablissement',
                'required' => false,
                'class' => Etablissement::class,
                'choice_label' => 'nom'
            ])
            ->add('username', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => "Nom d'utilisateur*"
            ])
            ->add('nom_affichage', TextType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Nom Affichage*'
            ])
            ->add('email', EmailType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Email*'
            ])
            ->add('password', PasswordType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Mot de Passe*'
            ])
            ->add('biographie', TextareaType::class, [
                'attr' => ['class' => 'form-control'],
                'label' => 'Biographie*'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Compte::class,
        ]);
    }
}
