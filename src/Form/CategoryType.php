<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Validator\Constraints\File as ConstraintFile;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('imagePath')
            ->add('image', SymfonyFileType::class, [
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new ConstraintFile([
                        'maxSize' => '5M',
                        'mimeTypes' => [
                             'image/jpeg',
                             'image/png',
                             'image/jpg',
                         ]
                    ])
                ],
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}