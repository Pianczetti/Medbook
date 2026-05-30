<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class Medbook_bookingSlotsModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    public function init(): void
    {
        parent::init();
        $this->ajax = true;
    }

    public function displayAjax(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        $resourceId = (int) Tools::getValue('resource_id', 0);
        $date = (string) Tools::getValue('date', '');

        if (!$resourceId || !$date) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => 'Missing resource_id or date parameter.',
                'slots' => [],
            ]));

            return;
        }

        // Validate date format
        $dateObj = \DateTime::createFromFormat('Y-m-d', $date);
        if (!$dateObj || $dateObj->format('Y-m-d') !== $date) {
            $this->ajaxRender(json_encode([
                'success' => false,
                'error' => 'Invalid date format. Use YYYY-MM-DD.',
                'slots' => [],
            ]));

            return;
        }

        $provider = $this->module->get('MedBook\\Booking\\Service\\BookingFrontProvider');
        $langId = (int) $this->context->language->id;

        $showPrices = (bool) Configuration::get('MEDBOOK_BOOKING_SHOW_PRICES');

        // Fetch resource data once and reuse below
        $resourceData = $provider->getResourceWithPricing($resourceId, $langId);

        // Determine if this resource has variable duration
        $minDuration = $resourceData ? (int) $resourceData['min_duration_minutes'] : 0;
        $maxDuration = $resourceData ? (int) $resourceData['max_duration_minutes'] : 0;
        $hasVariableDuration = $minDuration > 0 && $maxDuration > 0 && $minDuration !== $maxDuration;

        // Enrich slots with pricing data and duration options
        $enrichedSlots = [];

        if ($hasVariableDuration) {
            // Use rich structure that includes durations per slot
            $slotsWithDurations = $provider->getAvailableSlotsWithDurations($resourceId, $date, $langId);

            if ($showPrices) {
                $pricingService = $this->module->get('MedBook\\Booking\\Service\\PricingService');

                foreach ($slotsWithDurations as $slot) {
                    $startMinutes = $this->timeToMinutes($slot['time']);
                    // Calculate price using minimum duration as base
                    $timeEnd = $this->minutesToTime($startMinutes + $minDuration);
                    $price = $pricingService->calculateSlotPrice($resourceId, $date, $slot['time'], $timeEnd);

                    $enrichedSlots[] = [
                        'time' => $slot['time'],
                        'price' => $price,
                        'formatted_price' => number_format($price, 2) . ' ' . $this->context->currency->sign,
                        'durations' => $slot['durations'],
                    ];
                }
            } else {
                foreach ($slotsWithDurations as $slot) {
                    $enrichedSlots[] = [
                        'time' => $slot['time'],
                        'durations' => $slot['durations'],
                    ];
                }
            }
        } else {
            // Fixed duration: use flat slots
            $slots = $provider->getAvailableSlots($resourceId, $date, $langId);

            if ($showPrices) {
                $pricingService = $this->module->get('MedBook\\Booking\\Service\\PricingService');
                $durationMinutes = $resourceData ? (int) $resourceData['duration_minutes'] : 30;

                foreach ($slots as $slot) {
                    $startMinutes = $this->timeToMinutes($slot);
                    $timeEnd = $this->minutesToTime($startMinutes + $durationMinutes);
                    $price = $pricingService->calculateSlotPrice($resourceId, $date, $slot, $timeEnd);

                    $enrichedSlots[] = [
                        'time' => $slot,
                        'price' => $price,
                        'formatted_price' => number_format($price, 2) . ' ' . $this->context->currency->sign,
                    ];
                }
            } else {
                foreach ($slots as $slot) {
                    $enrichedSlots[] = [
                        'time' => $slot,
                    ];
                }
            }
        }

        // Resource info
        $resourceInfo = [];
        if ($resourceData) {
            $resourceInfo = [
                'base_price' => (float) $resourceData['base_price'],
                'min_duration_minutes' => (int) $resourceData['min_duration_minutes'],
                'max_duration_minutes' => (int) $resourceData['max_duration_minutes'],
                'duration_minutes' => (int) $resourceData['duration_minutes'],
            ];
        }

        $this->ajaxRender(json_encode([
            'success' => true,
            'slots' => $enrichedSlots,
            'date' => $date,
            'resource_id' => $resourceId,
            'resource' => $resourceInfo,
        ]));
    }

    private function timeToMinutes(string $time): int
    {
        $parts = explode(':', $time);

        return ((int) $parts[0]) * 60 + ((int) ($parts[1] ?? 0));
    }

    private function minutesToTime(int $minutes): string
    {
        $hours = intdiv($minutes, 60);
        $mins = $minutes % 60;

        return sprintf('%02d:%02d', $hours, $mins);
    }
}
