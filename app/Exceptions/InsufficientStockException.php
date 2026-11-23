<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    /**
     * @param  list<array{ingredient_id: int, name: string, needed: string, available: string}>  $shortages
     */
    public function __construct(
        string $message,
        public readonly array $shortages = [],
    ) {
        parent::__construct($message);
    }
}
