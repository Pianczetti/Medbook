<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class RefundRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', IntegerType::class, [
                'label' => 'Resource ID (0 = global)',
                'required' => false,
                'data' => 0,
            ])
            ->add('hours_before', IntegerType::class, [
                'label' => 'Hours before booking',
                'required' => true,
            ])
            ->add('refund_percent', NumberType::class, [
                'label' => 'Refund percent (0-100)',
                'required' => true,
                'scale' => 2,
            ]);
    }
}
