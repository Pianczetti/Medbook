<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class MedicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa leku',
                'required' => true,
            ])
            ->add('dosage', TextType::class, [
                'label' => 'Dawkowanie',
                'required' => true,
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Ilosc',
                'required' => true,
            ])
            ->add('refunded', CheckboxType::class, [
                'label' => 'Refundacja',
                'required' => false,
            ]);
    }
}
