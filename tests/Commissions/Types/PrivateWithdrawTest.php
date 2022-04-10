<?php

namespace Payme\CommissionFeeCalculator\Tests\Commissions\Types;

use Payme\CommissionFeeCalculator\Commissions\Types\PrivateWithdraw;
use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\Services\CurrencyService;
use Payme\CommissionFeeCalculator\OperationCollection;
use PHPUnit\Framework\TestCase;

final class PrivateWithdrawTest extends TestCase
{
    private $currencyService;
    private $operation;
    private $operationCollection;
    private $amount;

    public function setUp()
    {
        $this->currencyService = $this->createMock(CurrencyService::class);
        $this->operation = $this->createMock(Operation::class);
        $this->operationCollection = $this->createMock(OperationCollection::class);
        $this->amount = $this->createMock(Amount::class);
    }

    public function testWillReturnAmount()
    {
        $this->operation
            ->expects($this->atLeastOnce())
            ->method('getAmount')
            ->willReturn($this->amount);

        $this->operationCollection
            ->expects($this->atLeastOnce())
            ->method('getOperations')
            ->willReturn([$this->operation]);

        $this->currencyService
            ->expects($this->atLeastOnce())
            ->method('subAmount')
            ->willReturn($this->amount);

        $this->currencyService
            ->expects($this->atLeastOnce())
            ->method('getPercentageOfAmount')
            ->willReturn($this->amount);

        $commission = new PrivateWithdraw($this->operation,
            $this->currencyService,
            $this->operationCollection
        );

        $commission->calculate();

        $this->assertInstanceOf(
            Amount::class,
            $commission->calculate()
        );
    }
}
