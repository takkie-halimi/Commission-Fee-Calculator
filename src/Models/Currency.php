<?php

namespace Payme\CommissionFeeCalculator\Models;

class Currency
{
    protected $symbol;
    protected $rate;
    protected $precision;

    public function __construct($symbol, $rate, $precision)
    {
        $this->symbol    = $symbol;
        $this->rate      = $rate;
        $this->precision = $precision;
    }

    public function getRate() { return $this->rate; }

    public function getPrecision() { return $this->precision; }
}
