<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClinicType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nazwa',
                'required' => true,
            ])
            ->add('address', TextType::class, [
                'label' => 'Adres',
                'required' => true,
            ])
            ->add('city', TextType::class, [
                'label' => 'Miasto',
                'required' => true,
            ])
            ->add('postal_code', TextType::class, [
                'label' => 'Kod pocztowy',
                'required' => true,
                'attr' => ['maxlength' => 6, 'placeholder' => '00-000'],
            ])
            ->add('voivodeship', TextType::class, [
                'label' => 'Wojewodztwo',
                'required' => false,
            ])
            ->add('phone', TextType::class, [
                'label' => 'Telefon',
                'required' => false,
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
            ])
            ->add('opening_hours_json', TextareaType::class, [
                'label' => 'Godziny otwarcia (JSON)',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('is_active', SwitchType::class, [
                'label' => 'Aktywna',
                'required' => false,
            ]);
    }
}
