<?php

namespace Trekly\User\Domain\Project;

/**
 * Interfaz local de ProjectRepository para el servicio de usuarios
 * Esta es una copia simplificada para evitar dependencias entre microservicios
 */
interface ProjectRepository
{
    public function save($project): void;
    public function findById(string $id): ?object;
    public function findAll(array $filters = []): array;
}
