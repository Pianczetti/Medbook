<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', ChoiceType::class, [
                'label' => 'Resource',
                'required' => true,
                'choices' => [],
            ])
            ->add('day_of_week', ChoiceType::class, [
                'label' => 'Day of week',
                'required' => false,
                'choices' => [
                    'Sunday' => 0,
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                    'Saturday' => 6,
                ],
                'placeholder' => 'None (use specific date)',
            ])
            ->add('specific_date', DateType::class, [
                'label' => 'Specific date',
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('time_start', TextType::class, [
                'label' => 'Start time',
                'required' => true,
            ])
            ->add('time_end', TextType::class, [
                'label' => 'End time',
                'required' => true,
            ])
            ->add('is_available', SwitchType::class, [
                'label' => 'Available',
                'required' => false,
            ]);
    }
}
