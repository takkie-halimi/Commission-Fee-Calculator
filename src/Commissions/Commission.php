<?php

namespace Payme\CommissionFeeCalculator\Commissions;

use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\Services\CurrencyService;

abstract class Commission
{

    protected $operation;
    protected $currencyService;

    public function __construct(Operation $operation, CurrencyService $currencyService)
    {
        $this->operation     = $operation;
        $this->currencyService = $currencyService;
    }

    protected function getFee($rate, $feeAbleAmount = null): Amount
    {
        $amount = $feeAbleAmount ?? $this->operation->getAmount();
        return $this->currencyService->getPercentageOfAmount($amount, $rate);
    }
}
