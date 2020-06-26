<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ResetPasswordType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('newPassword', PasswordType::class,
             $this->getConfiguration("Nouveau mot de passe", "Tapez votre nouveau mot de passe ..."),[
                'label_attr' => ['for' =>'password']
            ])
            ->add('confirmPassword', PasswordType::class,
             $this->getConfiguration("Confirmation du mot de passe", "Confirmez votre nouveau mot de passe ..."),[
                'label_attr' => ['for' =>'password-custom']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
