<?php

namespace Gotribe\Payments\Domain;

interface CuentaPorPagarRepository
{
    public function create(array $data): void;
    public function findByCreatorAndProject(string $creatorId, string $projectId): ?array;
    public function registrarPago(array $data): void;
}
