<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Painting;
use App\Entity\Technical;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class PaintingType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     * @return void
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du tableau',
            ])
            ->add('author', TextType::class, [
                'label' => 'Nom de l\'auteur',
            ])
            ->add('makedAt', DateType::class, [
                'label' => 'Date de création du tableau',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description du tableau',
            ])
            ->add('height', IntegerType::class, [
                'label' => 'Hauteur du tableau',
            ])
            ->add('width', IntegerType::class, [
                'label' => 'Largeur du tableau',
            ])
            ->add('imageName', FileType::class, [
                'label' => 'Image du tableau',
                'mapped' => false,
                'required' => false,
                'constraints' => new File([
                    'mimeTypes' => [
                        'image/png',
                        'image/jpeg',
                        'image/jpg',
                        'image/webp'
                    ],
                    'mimeTypesMessage' => 'Le type d\'image "{{ type }}" est incorrecte. {{ types }} autorisés'
                ]),
                'data_class' => null
            ])
            ->add('isPublished', ChoiceType::class, [
                'label' => 'Afficher',
                'choices' => ['oui' => 1, 'non' => 0],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez...'
            ])
            ->add('technical', EntityType::class, [
                'class' => Technical::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisissez...'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Painting::class,
        ]);
    }
}
