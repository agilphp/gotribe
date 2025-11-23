<?php

namespace App\Domain\Currency;

interface CurrencyRepository
{
    /**
     * Find all active currencies
     * @return Currency[]
     */
    public function findAll(): array;

    /**
     * Find currency by code
     */
    public function findByCode(string $code): ?Currency;

    /**
     * Find currency by ID
     */
    public function findById(string $id): ?Currency;
}
