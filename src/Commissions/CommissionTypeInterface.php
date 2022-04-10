<?php

namespace Payme\CommissionFeeCalculator\Commissions;

interface CommissionTypeInterface {
    public function calculate();
}
