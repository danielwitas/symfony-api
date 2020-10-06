<?php

namespace App\Form;

use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'empty_data' => ''
            ])
            ->add('kcal', NumberType::class, [
                'empty_data' => ''
            ])
            ->add('weight', NumberType::class, [
                'empty_data' => ''
            ])
            ->add('protein', TextType::class, [
                'empty_data' => ''
            ])
            ->add('carbs', TextType::class, [
                'empty_data' => ''
            ])
            ->add('fat', TextType::class, [
                'empty_data' => ''
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'csrf_protection' => false,
        ]);
    }
}
