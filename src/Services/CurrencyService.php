<?php

namespace Payme\CommissionFeeCalculator\Services;

use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Currency;

class CurrencyService
{

    const ARITHMETIC_SCALE = 10;
    protected $currencies = [];


    public function collectCurrenciesFromArray(array $currencies): CurrencyService
    {
        foreach ($currencies as $currency) {
            $this->currencies[$currency['symbol']] = new Currency(...array_values($currency));
        }
        return $this;
    }

    public function convert(Amount $amount, $symbol): Amount
    {
        $multiplier = bcdiv(
            $this->getCurrencyRateForSymbol($symbol),
            $this->getCurrencyRateForSymbol($amount->getSymbol()),
            self::ARITHMETIC_SCALE
        );

        return new Amount(
            bcmul($amount->getAmount(), $multiplier, self::ARITHMETIC_SCALE),
            $symbol
        );
    }

    public function roundAndFormat(Amount $amount, $decimalPoint = '.', $thousandsSeparator = ''): string
    {
        $precision  = $this->getCurrencyPrecisionForSymbol($amount->getSymbol());
        $multiplier = bcpow(self::ARITHMETIC_SCALE, $precision);
        $newAmount  = bcdiv(
            ceil(bcmul($amount->getAmount(), $multiplier, self::ARITHMETIC_SCALE)),
            $multiplier,
            self::ARITHMETIC_SCALE
        );

        return number_format($newAmount, $precision, $decimalPoint, $thousandsSeparator);
    }


    public function getPercentageOfAmount(Amount $amount, $percentage): Amount
    {
        return new Amount(
            bcmul(
                bcdiv($amount->getAmount(), 100, self::ARITHMETIC_SCALE),
                $percentage,
                self::ARITHMETIC_SCALE
            ),
            $amount->getSymbol()
        );
    }


    public function isGreater(Amount $firstAmount, Amount $secondAmount): bool
    {
        return bccomp(
                $firstAmount->getAmount(),
                $this->convert($secondAmount, $firstAmount->getSymbol())->getAmount(),
                self::ARITHMETIC_SCALE
            ) === 1;
    }

    public function sumAmounts(Amount $firstAmount, Amount $secondAmount, $symbol): Amount
    {
        return new Amount(
            bcadd(
                $this->convert($firstAmount, $symbol)->getAmount(),
                $this->convert($secondAmount, $symbol)->getAmount(),
                self::ARITHMETIC_SCALE
            ),
            $symbol
        );
    }

    public function subAmount(Amount $firstAmount, Amount $secondAmount, $currencySymbol): Amount
    {
        return new Amount(
            bcsub(
                $this->convert($firstAmount, $currencySymbol)->getAmount(),
                $this->convert($secondAmount, $currencySymbol)->getAmount(),
                self::ARITHMETIC_SCALE
            ),
            $currencySymbol
        );
    }

    private function getCurrencyRateForSymbol($symbol)
    {
        return $this->getCurrencyOfSymbol($symbol)->getRate();
    }

    private function getCurrencyPrecisionForSymbol($symbol)
    {
        return $this->getCurrencyOfSymbol($symbol)->getPrecision();
    }

    private function getCurrencyOfSymbol($symbol)
    {
        if (isset($this->currencies[$symbol])) {
            return $this->currencies[$symbol];
        }
        throw new InvalidCurrencyException;
    }
}
