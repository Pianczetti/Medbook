<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BlockedDateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_resource', ChoiceType::class, [
                'label' => 'Resource',
                'required' => false,
                'choices' => [],
                'placeholder' => 'Global (all resources)',
            ])
            ->add('blocked_date', DateType::class, [
                'label' => 'Blocked date',
                'widget' => 'single_text',
                'required' => true,
            ])
            ->add('reason', TextType::class, [
                'label' => 'Reason',
                'required' => false,
            ]);
    }
}
