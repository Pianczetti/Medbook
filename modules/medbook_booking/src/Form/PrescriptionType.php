<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PrescriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('patient_name', TextType::class, [
                'label' => 'Imie i nazwisko pacjenta',
                'required' => true,
            ])
            ->add('pesel', TextType::class, [
                'label' => 'PESEL',
                'required' => false,
            ])
            ->add('doctor_name', TextType::class, [
                'label' => 'Lekarz',
                'required' => true,
            ])
            ->add('pwz_number', TextType::class, [
                'label' => 'Numer PWZ',
                'required' => true,
            ])
            ->add('diagnosis_code', TextType::class, [
                'label' => 'Kod ICD-10',
                'required' => false,
            ])
            ->add('medications', CollectionType::class, [
                'entry_type' => MedicationType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'label' => 'Leki',
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Uwagi',
                'required' => false,
            ]);
    }
}
