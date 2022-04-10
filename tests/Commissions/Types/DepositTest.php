<?php

namespace Payme\CommissionFeeCalculator\Tests\Commissions\Types;

use Payme\CommissionFeeCalculator\Commissions\Types\Deposit;
use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\Services\CurrencyService;
use PHPUnit\Framework\TestCase;

final class DepositTest extends TestCase
{

    private $currencyService;
    private $operation;
    private $amount;

    public function setUp()
    {
        $this->currencyService = $this->createMock(CurrencyService::class);
        $this->operation = $this->createMock(Operation::class);
        $this->amount = $this->createMock(Amount::class);
    }

    public function testWillReturnAmount()
    {
        $this->operation
            ->expects($this->atLeastOnce())
            ->method('getAmount')
            ->willReturn($this->amount);

        $this->currencyService
            ->expects($this->atLeastOnce())
            ->method('isGreater')
            ->willReturn($this->amount);

        $this->currencyService
            ->expects($this->atLeastOnce())
            ->method('getPercentageOfAmount')
            ->willReturn($this->amount);

        $commission = new Deposit($this->operation, $this->currencyService);
        $commission->calculate();

        $this->assertInstanceOf(
            Amount::class,
            $commission->calculate()
        );
    }
}
