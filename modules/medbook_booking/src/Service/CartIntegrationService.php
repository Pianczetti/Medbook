<?php

declare(strict_types=1);

namespace MedBook\Booking\Service;

use Doctrine\DBAL\Connection;

class CartIntegrationService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
        private readonly PricingService $pricingService,
    ) {
    }

    /**
     * Create a hidden virtual product for booking cart items.
     * Called during module install if product doesn't exist yet.
     *
     * @return int The created product ID
     */
    public function createVirtualProduct(): int
    {
        $productId = (int) \Configuration::get('MEDBOOK_BOOKING_PRODUCT_ID');
        if ($productId > 0) {
            return $productId;
        }

        $defaultLangId = (int) \Configuration::get('PS_LANG_DEFAULT');
        $defaultShopId = (int) \Configuration::get('PS_SHOP_DEFAULT');

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->connection->insert($this->dbPrefix . 'product', [
            'id_shop_default' => $defaultShopId,
            'is_virtual' => 1,
            'active' => 0,
            'visibility' => 'none',
            'price' => 0,
            'date_add' => $now,
            'date_upd' => $now,
        ]);

        $productId = (int) $this->connection->lastInsertId();

        $this->connection->insert($this->dbPrefix . 'product_shop', [
            'id_product' => $productId,
            'id_shop' => $defaultShopId,
            'active' => 0,
            'visibility' => 'none',
            'price' => 0,
            'date_add' => $now,
            'date_upd' => $now,
        ]);

        $this->connection->insert($this->dbPrefix . 'product_lang', [
            'id_product' => $productId,
            'id_shop' => $defaultShopId,
            'id_lang' => $defaultLangId,
            'name' => 'Booking Reservation',
            'link_rewrite' => 'booking-reservation',
            'description' => '',
            'description_short' => '',
        ]);

        \Configuration::updateValue('MEDBOOK_BOOKING_PRODUCT_ID', $productId);

        return $productId;
    }

    /**
     * Add a booking to the cart: calculate total, deposit, store in cart_data.
     *
     * @return array{success: bool, cart_data_id?: int, deposit_amount?: float, total_price?: float, error?: string}
     */
    public function addBookingToCart(
        int $cartId,
        int $resourceId,
        string $date,
        string $timeStart,
        string $timeEnd,
        array $addonIds,
        int $customerId,
    ): array {
        if (!$cartId || !$resourceId || !$date || !$timeStart || !$timeEnd) {
            return ['success' => false, 'error' => 'Missing required fields.'];
        }

        // Calculate slot price
        $slotPrice = $this->pricingService->calculateSlotPrice($resourceId, $date, $timeStart, $timeEnd);

        // Calculate add-on prices
        $addonsTotal = 0.0;
        if (!empty($addonIds)) {
            $addonsTotal = $this->calculateAddonsTotal($addonIds);
        }

        $totalPrice = round($slotPrice + $addonsTotal, 2);

        // Calculate deposit
        $depositAmount = $this->calculateDeposit($resourceId, $totalPrice);

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->connection->insert($this->dbPrefix . 'medbook_cart_data', [
            'id_cart' => $cartId,
            'id_resource' => $resourceId,
            'booking_date' => $date,
            'time_start' => $timeStart,
            'time_end' => $timeEnd,
            'addons_json' => !empty($addonIds) ? json_encode($addonIds) : null,
            'total_price' => $totalPrice,
            'deposit_amount' => $depositAmount,
            'date_add' => $now,
        ]);

        $cartDataId = (int) $this->connection->lastInsertId();

        // Add virtual product to the PS cart so checkout includes the booking price
        $this->addProductToCart($cartId, $depositAmount > 0 ? $depositAmount : $totalPrice, $customerId);

        return [
            'success' => true,
            'cart_data_id' => $cartDataId,
            'deposit_amount' => $depositAmount,
            'total_price' => $totalPrice,
        ];
    }

    /**
     * Finalize booking after order validation: read cart_data, create booking record.
     *
     * @return array{success: bool, booking_id?: int, reference_code?: string, error?: string}
     */
    public function finalizeBooking(int $cartId, int $orderId): array
    {
        $cartData = $this->getCartBookingData($cartId);

        if (!$cartData) {
            return ['success' => false, 'error' => 'No booking data found for this cart.'];
        }

        $referenceCode = 'BK' . strtoupper(bin2hex(random_bytes(5)));
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        // Get customer from cart
        $qb = $this->connection->createQueryBuilder();
        $qb->select('c.id_customer')
            ->from($this->dbPrefix . 'cart', 'c')
            ->where('c.id_cart = :cartId')
            ->setParameter('cartId', $cartId);
        $customerId = (int) $qb->executeQuery()->fetchOne();

        $this->connection->insert($this->dbPrefix . 'medbook_booking', [
            'id_resource' => $cartData['id_resource'],
            'id_customer' => $customerId ?: null,
            'id_order' => $orderId,
            'booking_date' => $cartData['booking_date'],
            'time_start' => $cartData['time_start'],
            'time_end' => $cartData['time_end'],
            'status' => 'confirmed',
            'customer_name' => '',
            'customer_email' => '',
            'total_price' => $cartData['total_price'],
            'deposit_paid' => $cartData['deposit_amount'],
            'reference_code' => $referenceCode,
            'date_add' => $now,
            'date_upd' => $now,
        ]);

        $bookingId = (int) $this->connection->lastInsertId();

        // Store booking addons
        if (!empty($cartData['addons_json'])) {
            $addonIds = json_decode($cartData['addons_json'], true);
            if (is_array($addonIds)) {
                foreach ($addonIds as $addonId) {
                    $addonPrice = $this->getAddonPrice((int) $addonId);
                    $this->connection->insert($this->dbPrefix . 'medbook_booking_addon', [
                        'id_booking' => $bookingId,
                        'id_addon' => (int) $addonId,
                        'price' => $addonPrice,
                    ]);
                }
            }
        }

        // Remove cart data after finalization
        $this->connection->delete(
            $this->dbPrefix . 'medbook_cart_data',
            ['id_cart_data' => (int) $cartData['id_cart_data']]
        );

        return [
            'success' => true,
            'booking_id' => $bookingId,
            'reference_code' => $referenceCode,
        ];
    }

    /**
     * Retrieve pending booking data for a cart.
     */
    public function getCartBookingData(int $cartId): ?array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('cd.*')
            ->from($this->dbPrefix . 'medbook_cart_data', 'cd')
            ->where('cd.id_cart = :cartId')
            ->orderBy('cd.date_add', 'DESC')
            ->setMaxResults(1)
            ->setParameter('cartId', $cartId);

        $result = $qb->executeQuery()->fetchAssociative();

        return $result ?: null;
    }

    /**
     * Remove expired cart data entries older than given hours.
     * Also removes orphaned SpecificPrice entries for the booking product.
     *
     * @return int Number of removed cart_data entries
     */
    public function cleanExpiredCartData(int $maxAgeHours = 24): int
    {
        $cutoff = (new \DateTime())->modify("-{$maxAgeHours} hours")->format('Y-m-d H:i:s');

        // First, find the cart IDs that are about to be expired
        $qb = $this->connection->createQueryBuilder();
        $qb->select('cd.id_cart')
            ->from($this->dbPrefix . 'medbook_cart_data', 'cd')
            ->where('cd.date_add < :cutoff')
            ->setParameter('cutoff', $cutoff);

        $expiredCartIds = $qb->executeQuery()->fetchFirstColumn();

        // Delete orphaned specific_price entries for these carts
        if (!empty($expiredCartIds)) {
            $productId = (int) \Configuration::get('MEDBOOK_BOOKING_PRODUCT_ID');
            if ($productId > 0) {
                $deleteSpQb = $this->connection->createQueryBuilder();
                $deleteSpQb->delete($this->dbPrefix . 'specific_price')
                    ->where('id_product = :productId')
                    ->andWhere('id_cart IN (:cartIds)')
                    ->setParameter('productId', $productId)
                    ->setParameter('cartIds', $expiredCartIds, Connection::PARAM_INT_ARRAY);
                $deleteSpQb->executeQuery();
            }
        }

        // Now delete the expired cart_data rows
        $qb2 = $this->connection->createQueryBuilder();
        $qb2->delete($this->dbPrefix . 'medbook_cart_data')
            ->where('date_add < :cutoff')
            ->setParameter('cutoff', $cutoff);

        return (int) $qb2->executeQuery()->rowCount();
    }

    /**
     * Calculate deposit amount based on deposit rule for a resource.
     */
    private function calculateDeposit(int $resourceId, float $totalPrice): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('dr.*')
            ->from($this->dbPrefix . 'medbook_deposit_rule', 'dr')
            ->where('(dr.id_resource = :resourceId OR dr.id_resource IS NULL)')
            ->andWhere('dr.is_active = 1')
            ->orderBy('dr.id_resource', 'DESC') // resource-specific first (non-null)
            ->setMaxResults(1)
            ->setParameter('resourceId', $resourceId);

        $rule = $qb->executeQuery()->fetchAssociative();

        if (!$rule) {
            return $totalPrice; // no deposit rule = full payment
        }

        if ($rule['deposit_type'] === 'percent') {
            return round($totalPrice * ((float) $rule['deposit_value'] / 100), 2);
        }

        // fixed amount
        return min((float) $rule['deposit_value'], $totalPrice);
    }

    /**
     * Calculate total price of selected add-ons.
     */
    private function calculateAddonsTotal(array $addonIds): float
    {
        if (empty($addonIds)) {
            return 0.0;
        }

        $qb = $this->connection->createQueryBuilder();
        $qb->select('SUM(a.price)')
            ->from($this->dbPrefix . 'medbook_addon', 'a')
            ->where('a.id_addon IN (:addonIds)')
            ->andWhere('a.is_active = 1')
            ->setParameter('addonIds', $addonIds, Connection::PARAM_INT_ARRAY);

        $result = $qb->executeQuery()->fetchOne();

        return $result !== false ? (float) $result : 0.0;
    }

    /**
     * Get the price of a single addon.
     */
    private function getAddonPrice(int $addonId): float
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select('a.price')
            ->from($this->dbPrefix . 'medbook_addon', 'a')
            ->where('a.id_addon = :addonId')
            ->setParameter('addonId', $addonId);

        $result = $qb->executeQuery()->fetchOne();

        return $result !== false ? (float) $result : 0.0;
    }

    /**
     * Add the virtual booking product to the PrestaShop cart and set a SpecificPrice
     * so the checkout total reflects the correct booking amount.
     */
    private function addProductToCart(int $cartId, float $price, int $customerId): void
    {
        $productId = (int) \Configuration::get('MEDBOOK_BOOKING_PRODUCT_ID');
        if ($productId <= 0) {
            $productId = $this->createVirtualProduct();
        }

        $cart = new \Cart($cartId);
        if (!\Validate::isLoadedObject($cart)) {
            return;
        }

        // Add 1 qty of the virtual product to the cart
        $cart->updateQty(1, $productId);

        // Create a SpecificPrice tied to this cart/customer so the price matches the booking amount
        $this->setBookingSpecificPrice($productId, $cartId, $customerId, $price);
    }

    /**
     * Create or update a SpecificPrice entry for the booking product in this cart.
     */
    private function setBookingSpecificPrice(int $productId, int $cartId, int $customerId, float $price): void
    {
        // Remove any previous specific price for this product+cart combination
        $qb = $this->connection->createQueryBuilder();
        $qb->delete($this->dbPrefix . 'specific_price')
            ->where('id_product = :productId')
            ->andWhere('id_cart = :cartId')
            ->setParameter('productId', $productId)
            ->setParameter('cartId', $cartId);
        $qb->executeQuery();

        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->connection->insert($this->dbPrefix . 'specific_price', [
            'id_product' => $productId,
            'id_product_attribute' => 0,
            'id_shop' => (int) \Configuration::get('PS_SHOP_DEFAULT'),
            'id_shop_group' => 0,
            'id_currency' => 0,
            'id_country' => 0,
            'id_group' => 0,
            'id_customer' => $customerId,
            'id_cart' => $cartId,
            'from_quantity' => 1,
            'price' => $price,
            'reduction' => 0,
            'reduction_type' => 'amount',
            'reduction_tax' => 1,
            'from' => '0000-00-00 00:00:00',
            'to' => '0000-00-00 00:00:00',
        ]);
    }
}
