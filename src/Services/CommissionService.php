<?php

namespace Payme\CommissionFeeCalculator\Services;

use Payme\CommissionFeeCalculator\Commissions\Types\Deposit;
use Payme\CommissionFeeCalculator\Commissions\Types\BusinessWithdraw;
use Payme\CommissionFeeCalculator\Commissions\Types\PrivateWithdraw;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\OperationCollection;
use Payme\CommissionFeeCalculator\Exceptions\InvalidOperationTypeException;
use Payme\CommissionFeeCalculator\Exceptions\InvalidUserTypeException;

class CommissionService
{

    protected $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    /**
     * @throws InvalidOperationTypeException
     * @throws InvalidUserTypeException
     */
    public function calculateFeesFromCollection(OperationCollection $operationsCollection): array
    {
        $fees = [];
        foreach ($operationsCollection->getOperations() as $operation) {
            $commission = $this->generateCommission(
                $this->currencyService,
                $operation,
                $operationsCollection
            );
            $fees[] = $this->currencyService->roundAndFormat($commission->calculate());
        }

        return $fees;
    }


    /**
     * @throws InvalidOperationTypeException
     * @throws InvalidUserTypeException
     */
    public function generateCommission(
        CurrencyService       $currencyService,
        Operation             $operation,
        OperationCollection $operationCollection
    ) {
        switch ($operation->getOperationType()) {
            case 'deposit':
                $commission = new Deposit($operation, $currencyService);
                break;
            case 'withdraw':
                switch ($operation->getUserType()) {
                    case 'private':
                        $commission = new PrivateWithdraw(
                            $operation,
                            $currencyService,
                            $operationCollection
                        );
                        break;
                    case 'business':
                        $commission = new BusinessWithdraw($operation, $currencyService);
                        break;
                    default:
                        throw new InvalidUserTypeException;
                }
                break;
            default:
                throw new InvalidOperationTypeException;
        }

        return $commission;
    }
}
