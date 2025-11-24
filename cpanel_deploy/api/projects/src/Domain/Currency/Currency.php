<?php

namespace Trekly\Project\Domain\Currency;

class Currency
{
    private string $id;
    private string $code;
    private string $name;
    private string $symbol;
    private bool $isActive;

    public function __construct(
        string $id,
        string $code,
        string $name,
        string $symbol,
        bool $isActive = true
    ) {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->symbol = $symbol;
        $this->isActive = $isActive;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'isActive' => $this->isActive
        ];
    }
}
