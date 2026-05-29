<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

class DepositRuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', IntegerType::class, [
                'label' => 'Resource ID (0 = global)',
                'required' => false,
                'data' => 0,
            ])
            ->add('deposit_type', ChoiceType::class, [
                'label' => 'Deposit type',
                'choices' => [
                    'Percent' => 'percent',
                    'Fixed' => 'fixed',
                ],
                'required' => true,
            ])
            ->add('deposit_value', NumberType::class, [
                'label' => 'Deposit value',
                'required' => true,
                'scale' => 2,
            ])
            ->add('is_active', SwitchType::class, [
                'label' => 'Active',
                'required' => false,
            ]);
    }
}
