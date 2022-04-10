<?php

namespace Payme\CommissionFeeCalculator\Commissions\Types;

use Payme\CommissionFeeCalculator\Commissions\Commission;
use Payme\CommissionFeeCalculator\Commissions\CommissionTypeInterface;
use Payme\CommissionFeeCalculator\Models\Amount;

class BusinessWithdraw extends Commission implements CommissionTypeInterface
{

    const COMMISSION_PERCENTAGE = 0.5;
    const MIN_COMMISSION = [
        'currency' => 'EUR',
        'fee'      => 0.5
    ];

    public function calculate(): Amount
    {
        $commission    = $this->getFee(self::COMMISSION_PERCENTAGE);
        $minCommission = new Amount(self::MIN_COMMISSION['fee'], self::MIN_COMMISSION['currency']);

        if ($this->currencyService->isGreater($minCommission, $commission)) {
            return $minCommission;
        }

        return $commission;
    }
}
