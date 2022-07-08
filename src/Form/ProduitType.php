<?php

namespace App\Form;

use App\Entity\Produit;
use App\Entity\Categorie;
use App\Form\ProduitType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('description')
            ->add('couleur')
            ->add('taille')
            ->add('photoForm', FileType::class, [
                "mapped" => false,
                "required" => false
            ])
            ->add('prix')
            ->add('stock')
            // selection de la categorie par son nom (categorie est une proprieté de Produit)
            ->add('categorie', EntityType::class,[
                'class' => Categorie::class, 
                'choice_label' => 'nom',
                'placeholder' => "Choisissez une categorie"
            ])

            ->add('Envoyer', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
