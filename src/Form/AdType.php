<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use App\Entity\Category;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends ApplicationType 
{
    
   
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        

        $builder
            ->add(
                'title', 
                TextType::class, 
                $this->getConfiguration("Titre", "Tapez un super titre pour votre annonce"))
            ->add(
                'introduction',
                 TextType::class,
                 $this->getConfiguration("Introduction", "Donnez une description globale de l'annonce"))
            ->add(
                'content', 
                TextareaType::class, 
                $this->getConfiguration("Description détaillée", "Tapez une description qui donne vraiment envie de venir chez vous !"))
            ->add(
                'imageFile', FileType::class, [
                'required' => false,
                ],
                $this->getConfiguration("URL de l'image principale", "Donnez l'adresse d'une image qui donne vraiment envie"))
            ->add(
                'price', 
                MoneyType::class, 
                $this->getConfiguration("Prix par jour", "Indiquez le prix que vous voulez pour une journée"))
        
            ->add(
                'images',
                CollectionType::class,
                [
                    'entry_type' => ImageType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            )
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
