<?php

namespace App\Application;

use App\Domain\Currency\CurrencyRepository;

class GetCurrenciesUseCase
{
    private CurrencyRepository $currencyRepository;

    public function __construct(CurrencyRepository $currencyRepository)
    {
        $this->currencyRepository = $currencyRepository;
    }

    public function execute(): array
    {
        $currencies = $this->currencyRepository->findAll();
        
        return array_map(function ($currency) {
            return $currency->toArray();
        }, $currencies);
    }
}
