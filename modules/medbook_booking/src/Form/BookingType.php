<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BookingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', ChoiceType::class, [
                'label' => 'Resource',
                'required' => true,
                'choices' => [],
            ])
            ->add('booking_date', DateType::class, [
                'label' => 'Booking date',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('time_start', TextType::class, [
                'label' => 'Start time',
                'required' => true,
            ])
            ->add('time_end', TextType::class, [
                'label' => 'End time',
                'required' => true,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Status',
                'choices' => [
                    'Pending' => 'pending',
                    'Confirmed' => 'confirmed',
                    'Cancelled' => 'cancelled',
                    'Completed' => 'completed',
                    'No show' => 'no_show',
                ],
                'required' => true,
            ])
            ->add('customer_name', TextType::class, [
                'label' => 'Customer name',
                'required' => true,
            ])
            ->add('customer_email', EmailType::class, [
                'label' => 'Customer email',
                'required' => true,
            ])
            ->add('customer_phone', TextType::class, [
                'label' => 'Customer phone',
                'required' => false,
            ])
            ->add('notes', TextareaType::class, [
                'label' => 'Notes',
                'required' => false,
            ])
            ->add('no_show', CheckboxType::class, [
                'label' => 'No-show',
                'required' => false,
            ])
            ->add('admin_notes', TextareaType::class, [
                'label' => 'Admin Notes',
                'required' => false,
                'attr' => ['rows' => 4],
            ])
            ->add('visit_type', ChoiceType::class, [
                'label' => 'Typ wizyty',
                'choices' => [
                    'Stacjonarna' => 'stacjonarna',
                    'Online' => 'online',
                ],
                'required' => true,
                'data' => 'stacjonarna',
            ])
            ->add('insurance_type', ChoiceType::class, [
                'label' => 'Ubezpieczenie',
                'choices' => [
                    'NFZ' => 'NFZ',
                    'Prywatne' => 'prywatne',
                    'Pakiet' => 'pakiet',
                ],
                'required' => true,
                'data' => 'prywatne',
            ]);
    }
}
