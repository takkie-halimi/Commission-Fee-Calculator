<?php

namespace Payme\CommissionFeeCalculator\Tests;

use Payme\CommissionFeeCalculator\Commissions\Types\PrivateWithdraw;
use Payme\CommissionFeeCalculator\Services\CommissionService;
use Payme\CommissionFeeCalculator\Commissions\Types\Deposit;
use Payme\CommissionFeeCalculator\Commissions\Types\BusinessWithdraw;
use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\Services\CurrencyService;
use Payme\CommissionFeeCalculator\OperationCollection;
use PHPUnit\Framework\TestCase;

final class CommissionServiceTest extends TestCase
{

    private $currencyService;
    private $operationCollection;
    private $operation;
    private $commissionType;
    private $amount;

    public function setUp()
    {
        $this->currencyService = $this->createMock(CurrencyService::class);
        $this->operationCollection = $this->createMock(OperationCollection::class);
        $this->operation = $this->createMock(Operation::class);
        $this->commissionType = $this->createMock(Deposit::class);
        $this->amount = $this->createMock(Amount::class);
    }

    public function testCalculateFeeFromCollectionWillReturnCorrectSizeOfArray()
    {
        $operations = [];
        for ($i = 0; $i < rand(1, 10); $i++) {
            $operations[] = $this->operation;
        }
        $operationCount = count($operations);

        $this->operationCollection
            ->expects($this->once())
            ->method('getOperations')
            ->willReturn($operations);

        $this->currencyService
            ->expects($this->exactly($operationCount))
            ->method('roundAndFormat')
            ->willReturn('1');

        $this->commissionType
            ->expects($this->exactly($operationCount))
            ->method('calculate')
            ->willReturn($this->amount);

        $stub = $this->getMockBuilder(CommissionService::class)
            ->setConstructorArgs([$this->currencyService])
            ->setMethods(['generateCommission'])
            ->getMock();

        $stub->method('generateCommission')
            ->willReturn($this->commissionType);

        $this->assertCount($operationCount, $stub->calculateFeesFromCollection($this->operationCollection));
    }

    /**
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidOperationTypeException
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidUserTypeException
     */
    public function testGeneratesDepositCommission()
    {
        $this->operation
            ->expects($this->once())
            ->method('getOperationType')
            ->willReturn('Deposit');

        $calculator = new CommissionService($this->currencyService);

        $this->assertInstanceOf(
            Deposit::class,
            $calculator->generateCommission($this->currencyService, $this->operation, $this->operationCollection)
        );
    }

    /**
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidOperationTypeException
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidUserTypeException
     */
    public function testGeneratesBusinessWithdrawCommission()
    {
        $this->operation
            ->expects($this->once())
            ->method('getOperationType')
            ->willReturn('withdraw');

        $this->operation
            ->expects($this->once())
            ->method('getUserType')
            ->willReturn('business');

        $calculator = new CommissionService($this->currencyService);

        $this->assertInstanceOf(
            BusinessWithdraw::class,
            $calculator->generateCommission($this->currencyService, $this->operation, $this->operationCollection)
        );
    }

    /**
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidOperationTypeException
     * @throws \Payme\CommissionFeeCalculator\Exceptions\InvalidUserTypeException
     */
    public function testGeneratesPrivateWithdrawCommission()
    {
        $this->operation
            ->expects($this->once())
            ->method('getOperationType')
            ->willReturn('withdraw');

        $this->operation
            ->expects($this->once())
            ->method('getUserType')
            ->willReturn('private');

        $calculator = new CommissionService($this->currencyService);

        $this->assertInstanceOf(
            PrivateWithdraw::class,
            $calculator->generateCommission($this->currencyService, $this->operation, $this->operationCollection)
        );
    }
}
