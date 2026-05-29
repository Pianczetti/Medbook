<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class DoctorProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', IntegerType::class, [
                'label' => 'ID zasobu (lekarza)',
                'required' => true,
            ])
            ->add('id_specialization', IntegerType::class, [
                'label' => 'Specjalizacja (ID)',
                'required' => false,
            ])
            ->add('education', TextareaType::class, [
                'label' => 'Wyksztalcenie',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('experience_years', IntegerType::class, [
                'label' => 'Lata doswiadczenia',
                'required' => false,
                'data' => 0,
            ])
            ->add('languages', TextType::class, [
                'label' => 'Jezyki',
                'required' => false,
                'attr' => ['placeholder' => 'np. polski, angielski'],
            ])
            ->add('certifications', TextareaType::class, [
                'label' => 'Certyfikaty',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('photo', TextType::class, [
                'label' => 'Zdjecie (URL)',
                'required' => false,
            ])
            ->add('nip', TextType::class, [
                'label' => 'Numer NIP',
                'required' => false,
                'attr' => ['maxlength' => 10],
            ])
            ->add('pwz_number', TextType::class, [
                'label' => 'Numer PWZ',
                'required' => false,
                'attr' => ['maxlength' => 7],
            ])
            ->add('consultation_online', SwitchType::class, [
                'label' => 'Konsultacja online',
                'required' => false,
            ])
            ->add('consultation_inperson', SwitchType::class, [
                'label' => 'Konsultacja stacjonarna',
                'required' => false,
            ]);
    }
}
