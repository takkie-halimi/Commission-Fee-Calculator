<?php

namespace Payme\CommissionFeeCalculator\Commissions\Types;

use Payme\CommissionFeeCalculator\Commissions\Commission;
use Payme\CommissionFeeCalculator\Commissions\CommissionTypeInterface;
use Payme\CommissionFeeCalculator\Models\Amount;

class Deposit extends Commission implements CommissionTypeInterface
{

    const PRIVATE_COMMISSION_PERCENTAGE = 0.03;

    const MAX_COMMISSION = [
        'currency' => 'EUR',
        'fee'      => 5
    ];

    public function calculate(): Amount
    {
        $commission    = $this->getFee(self::PRIVATE_COMMISSION_PERCENTAGE);
        $maxCommission = new Amount(self::MAX_COMMISSION['fee'], self::MAX_COMMISSION['currency']);

        if ($this->currencyService->isGreater($commission, $maxCommission)) {
            return $maxCommission;
        }

        return $commission;
    }
}
