<?php
namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Taxes;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType as SymfonyFileType;
use Symfony\Component\Validator\Constraints\File as ConstraintFile;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('priceHT')
            ->add('img', TextType::class, [
                'required' => false,
            ])
            /*->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => true,
            ])*/
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
            ->add('public', CheckboxType::class, [
                'required' => false,
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('taxes', EntityType::class, [
                'class' => Taxes::class,
                'choice_label' => 'wordwording',
            ]);        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}