<?php

namespace Payme\CommissionFeeCalculator\Models;


class Operation
{
    protected $operationId;
    protected $operationDate;
    protected $userID;
    protected $userType;
    protected $operationType;
    protected $amount;

    public function __construct(
        $operationId,
        \DateTime $operationDate,
        $userID,
        $userType,
        $operationType,
        Amount $amount

    ) {
        $this->operationId = $operationId;
        $this->operationDate = $operationDate;
        $this->userID = $userID;
        $this->userType = $userType;
        $this->operationType = $operationType;
        $this->amount = $amount;
    }

    public function getOperationId() { return $this->operationId; }

    public function getUserType() { return $this->userType; }

    public function getOperationType() { return $this->operationType; }

    public function getAmount(): Amount { return $this->amount; }

    public function getAmountSymbol() { return $this->amount->getSymbol();}

    public function getUserID() { return $this->userID; }

    public function getOperationDate(): \DateTime { return $this->operationDate; }

}
