<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('brand')
            ->add('price')
            ->add('quantity')
            ->add('category',
                    EntityType::class,
                    [
                        'class' => Category::class,
                        'choice_label' => 'name',
                        'multiple' => false,
                        'expanded' => false
                    ]
            )
            ->add('image', 
                    FileType::class,
                    [
                        'data_class' => null,
                        'required' => is_null ($builder->getData()->getImage())
                    ]
            )

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
