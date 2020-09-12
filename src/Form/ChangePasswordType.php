<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('password', PasswordType::class, [
                'empty_data' => ''
            ])
            ->add('newPassword', PasswordType::class, [
                'empty_data' => ''
            ])
            ->add('repeatNewPassword', PasswordType::class, [
                'empty_data' => ''
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['change-password'],
            'data_class' => User::class,
            'csrf_protection' => false
        ]);
    }
}
