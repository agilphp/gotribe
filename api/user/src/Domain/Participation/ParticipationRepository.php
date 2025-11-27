<?php

namespace Trekly\User\Domain\Participation;

/**
 * Interfaz local de ParticipationRepository para el servicio de usuarios
 * Esta es una copia simplificada para evitar dependencias entre microservicios
 */
interface ParticipationRepository
{
    public function save($participation): void;
    public function findById(string $id): ?object;
    public function findByProjectAndUser(string $projectId, string $userId): ?object;
}
