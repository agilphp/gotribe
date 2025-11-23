<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Currency\Currency;
use App\Domain\Currency\CurrencyRepository;
use PDO;

class DoctrineCurrencyRepository implements CurrencyRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, name, symbol, is_active 
             FROM currencies 
             WHERE is_active = 1 
             ORDER BY code ASC'
        );
        $stmt->execute();

        $currencies = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $currencies[] = new Currency(
                $row['id'],
                $row['code'],
                $row['name'],
                $row['symbol'],
                (bool) $row['is_active']
            );
        }

        return $currencies;
    }

    public function findByCode(string $code): ?Currency
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, name, symbol, is_active 
             FROM currencies 
             WHERE code = :code'
        );
        $stmt->execute(['code' => $code]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Currency(
            $row['id'],
            $row['code'],
            $row['name'],
            $row['symbol'],
            (bool) $row['is_active']
        );
    }

    public function findById(string $id): ?Currency
    {
        $stmt = $this->pdo->prepare(
            'SELECT id, code, name, symbol, is_active 
             FROM currencies 
             WHERE id = :id'
        );
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return new Currency(
            $row['id'],
            $row['code'],
            $row['name'],
            $row['symbol'],
            (bool) $row['is_active']
        );
    }
}
