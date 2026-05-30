<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SpecializationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'required' => true,
            ])
            ->add('slug', TextType::class, [
                'label' => 'Slug',
                'required' => true,
            ])
            ->add('icon', TextType::class, [
                'label' => 'Ikona',
                'required' => false,
                'attr' => ['placeholder' => 'np. fa-heart'],
            ])
            ->add('position', IntegerType::class, [
                'label' => 'Pozycja',
                'required' => false,
                'data' => 0,
            ])
            ->add('is_active', SwitchType::class, [
                'label' => 'Aktywna',
                'required' => false,
            ]);
    }
}
