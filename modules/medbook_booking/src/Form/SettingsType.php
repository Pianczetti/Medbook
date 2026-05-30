<?php

declare(strict_types=1);

namespace MedBook\Booking\Form;

use PrestaShopBundle\Form\Admin\Type\SwitchType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class SettingsType extends AbstractType
{
    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('confirm_mode', ChoiceType::class, [
                'label' => $this->translator->trans('Confirmation mode', [], 'Modules.Medbookbooking.Admin'),
                'choices' => [
                    $this->translator->trans('Automatic', [], 'Modules.Medbookbooking.Admin') => 'auto',
                    $this->translator->trans('Manual', [], 'Modules.Medbookbooking.Admin') => 'manual',
                ],
                'required' => true,
            ])
            ->add('max_days_ahead', IntegerType::class, [
                'label' => $this->translator->trans('Max days ahead', [], 'Modules.Medbookbooking.Admin'),
                'required' => false,
                'data' => 30,
            ])
            ->add('min_hours_advance', IntegerType::class, [
                'label' => $this->translator->trans('Min hours in advance', [], 'Modules.Medbookbooking.Admin'),
                'required' => false,
                'data' => 2,
            ])
            ->add('allow_guests', SwitchType::class, [
                'label' => $this->translator->trans('Allow guest bookings', [], 'Modules.Medbookbooking.Admin'),
                'required' => false,
            ])
            ->add('slot_duration', ChoiceType::class, [
                'label' => $this->translator->trans('Slot duration', [], 'Modules.Medbookbooking.Admin'),
                'choices' => [
                    '15 min' => 15,
                    '30 min' => 30,
                    '60 min' => 60,
                ],
                'required' => true,
            ])
            ->add('email_notifications', SwitchType::class, [
                'label' => $this->translator->trans('Email notifications', [], 'Modules.Medbookbooking.Admin'),
                'required' => false,
            ])
            ->add('pricing_mode', ChoiceType::class, [
                'label' => $this->translator->trans('Pricing mode', [], 'Modules.Medbookbooking.Admin'),
                'choices' => [
                    $this->translator->trans('Cumulative', [], 'Modules.Medbookbooking.Admin') => 'cumulative',
                    $this->translator->trans('Highest priority', [], 'Modules.Medbookbooking.Admin') => 'highest_priority',
                ],
                'required' => true,
            ])
            ->add('show_prices', SwitchType::class, [
                'label' => $this->translator->trans('Show prices', [], 'Modules.Medbookbooking.Admin'),
                'required' => false,
            ]);
    }
}
