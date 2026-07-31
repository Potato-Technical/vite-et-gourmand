<?php

declare(strict_types=1);

namespace App\Services;

use InvalidArgumentException;

final class PricingService
{
    private const DISCOUNT_RATE = 10;
    private const EXTRA_PEOPLE_FOR_DISCOUNT = 5;
    private const DELIVERY_FEE_OUTSIDE_BORDEAUX_CENTS = 500;

    /**
     * @return array{
     *     price_before_discount_cents: int,
     *     discount_rate: int,
     *     discount_amount_cents: int,
     *     delivery_fee_cents: int,
     *     total_cents: int
     * }
     */
    public function calculate(
        int $minimumPriceCents,
        int $minimumPeople,
        int $people,
        string $city
    ): array {
        if ($minimumPriceCents < 0) {
            throw new InvalidArgumentException(
                'Le prix minimum ne peut pas être négatif.'
            );
        }

        if ($minimumPeople <= 0) {
            throw new InvalidArgumentException(
                'Le nombre minimum de personnes doit être positif.'
            );
        }

        if ($people < $minimumPeople) {
            throw new InvalidArgumentException(
                'Le nombre de personnes est inférieur au minimum du menu.'
            );
        }

        if (trim($city) === '') {
            throw new InvalidArgumentException(
                'La ville est obligatoire.'
            );
        }

        $priceBeforeDiscountCents = self::divideAndRound(
            $minimumPriceCents * $people,
            $minimumPeople
        );

        $discountRate = $people >= (
            $minimumPeople + self::EXTRA_PEOPLE_FOR_DISCOUNT
        )
            ? self::DISCOUNT_RATE
            : 0;

        $discountAmountCents = self::divideAndRound(
            $priceBeforeDiscountCents * $discountRate,
            100
        );

        $deliveryFeeCents = strcasecmp(
            trim($city),
            'Bordeaux'
        ) === 0
            ? 0
            : self::DELIVERY_FEE_OUTSIDE_BORDEAUX_CENTS;

        $totalCents = (
            $priceBeforeDiscountCents
            - $discountAmountCents
            + $deliveryFeeCents
        );

        return [
            'price_before_discount_cents' =>
                $priceBeforeDiscountCents,

            'discount_rate' =>
                $discountRate,

            'discount_amount_cents' =>
                $discountAmountCents,

            'delivery_fee_cents' =>
                $deliveryFeeCents,

            'total_cents' =>
                $totalCents,
        ];
    }

    private static function divideAndRound(
        int $numerator,
        int $denominator
    ): int {
        if ($denominator <= 0) {
            throw new InvalidArgumentException(
                'Le diviseur doit être strictement positif.'
            );
        }

        return intdiv(
            $numerator + intdiv($denominator, 2),
            $denominator
        );
    }
}