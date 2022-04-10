<?php

namespace Payme\CommissionFeeCalculator;

use League\Csv\Reader;
use Payme\CommissionFeeCalculator\Models\Amount;
use Payme\CommissionFeeCalculator\Models\Operation;
use Payme\CommissionFeeCalculator\Exceptions\FileNotFoundException;

class OperationCollection
{

    protected $operations = [];

    /**
     * @throws \Exception
     */
    public function parseFromCSV($path, $append = false)
    {
        if (!file_exists($path)) {
            throw new FileNotFoundException;
        }
        $this->operations = $append ? $this->operations : [];
        foreach (Reader::createFromPath($path) as $Line) {
            $this->add(new Operation(
                $this->generateTransactionID(),
                new \DateTime($Line[0]),
                $Line[1],
                $Line[2],
                $Line[3],
                new Amount($Line[4], $Line[5])
            ));
        }
    }

    public function add(Operation $Operation): OperationCollection
    {
        $this->operations[] = $Operation;
        return $this;
    }

    public function getOperations(): array { return $this->operations; }

    private function generateTransactionID(): int
    {
        $operations = $this->getOperations();
        return $this->isEmpty() ? 1 : end($operations)->getOperationId() + 1; }

    private function isEmpty(): bool
    { return empty($this->getOperations()); }

}
