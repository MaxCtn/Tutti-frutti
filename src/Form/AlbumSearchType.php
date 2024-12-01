<?php

// src/Form/AlbumSearchType.php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire de recherche d'albums à partir de fruits.
 */
class AlbumSearchType extends AbstractType
{
    /**
     * Construit le formulaire de recherche d'albums.
     *
     * @param FormBuilderInterface $builder Le constructeur de formulaire.
     * @param array $options Les options supplémentaires pour le formulaire.
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('searchTerm', SearchType::class, [
                'label' => 'Recherche de fruits',
                'attr' => [
                    'placeholder' => 'Entrez un fruit (ex : banane, pomme, etc.)',
                    'class' => 'form-control'
                ]
            ]);
    }

    /**
     * Configure les options par défaut du formulaire.
     *
     * @param OptionsResolver $resolver Le résolveur d'options.
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true, // Active la protection CSRF.
        ]);
    }
}
