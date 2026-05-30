<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

class PriceRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('id_resource', IntegerType::class, [
                'label' => 'Resource ID (0 = all)',
                'required' => false,
                'data' => 0,
            ])
            ->add('date_from', DateType::class, [
                'label' => 'Date from',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('date_to', DateType::class, [
                'label' => 'Date to',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('day_of_week', ChoiceType::class, [
                'label' => 'Day of week',
                'required' => false,
                'placeholder' => 'All days',
                'choices' => [
                    'Monday' => 1,
                    'Tuesday' => 2,
                    'Wednesday' => 3,
                    'Thursday' => 4,
                    'Friday' => 5,
                    'Saturday' => 6,
                    'Sunday' => 7,
                ],
            ])
            ->add('time_from', TimeType::class, [
                'label' => 'Time from',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('time_to', TimeType::class, [
                'label' => 'Time to',
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('modifier_type', ChoiceType::class, [
                'label' => 'Modifier type',
                'choices' => [
                    'Percent' => 'percent',
                    'Fixed' => 'fixed',
                ],
                'required' => true,
            ])
            ->add('modifier_value', NumberType::class, [
                'label' => 'Modifier value',
                'required' => true,
                'scale' => 2,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'Priority',
                'required' => false,
                'data' => 0,
            ])
            ->add('is_active', SwitchType::class, [
                'label' => 'Active',
                'required' => false,
            ]);
    }
}
