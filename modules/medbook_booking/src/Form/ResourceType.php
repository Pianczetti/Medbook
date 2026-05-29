<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ResourceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
                'required' => true,
            ])
            ->add('resource_type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Doctor' => 'doctor',
                    'Table' => 'table',
                    'Service' => 'service',
                    'Custom' => 'custom',
                ],
                'required' => true,
            ])
            ->add('capacity', IntegerType::class, [
                'label' => 'Capacity',
                'required' => false,
                'data' => 1,
            ])
            ->add('duration_minutes', IntegerType::class, [
                'label' => 'Duration (minutes)',
                'required' => false,
                'data' => 30,
            ])
            ->add('base_price', NumberType::class, [
                'label' => 'Base price',
                'required' => false,
                'scale' => 2,
                'data' => 0.00,
            ])
            ->add('min_duration_minutes', IntegerType::class, [
                'label' => 'Min duration (minutes)',
                'required' => false,
                'data' => 0,
            ])
            ->add('max_duration_minutes', IntegerType::class, [
                'label' => 'Max duration (minutes)',
                'required' => false,
                'data' => 0,
            ])
            ->add('buffer_minutes', IntegerType::class, [
                'label' => 'Buffer (minutes)',
                'required' => false,
                'data' => 0,
            ])
            ->add('color', TextType::class, [
                'label' => 'Color',
                'required' => false,
                'data' => '#3498db',
            ])
            ->add('is_active', SwitchType::class, [
                'label' => 'Active',
                'required' => false,
            ])
            ->add('id_product', IntegerType::class, [
                'label' => 'Linked Product ID',
                'required' => false,
                'attr' => ['placeholder' => 'PrestaShop Product ID (optional)'],
            ]);
    }
}
