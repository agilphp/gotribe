<?php

namespace Trekly\Auth\Infrastructure\Persistence;

use Trekly\Auth\Domain\User\Email;
use Trekly\Auth\Domain\User\Password;
use Trekly\Auth\Domain\User\Role;
use Trekly\Auth\Domain\User\User;
use Trekly\Auth\Domain\User\UserId;
use Trekly\Auth\Domain\User\UserRepository;

class PDOUserRepository implements UserRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(User $user): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO users (id, email, password_hash, role, created_at)
            VALUES (:id, :email, :password, :role, :created_at)
        ");

        $stmt->execute([
            ':id' => (string) $user->getId(),
            ':email' => (string) $user->getEmail(),
            ':password' => (string) $user->getPassword(),
            ':role' => $user->getRole()->value,
            ':created_at' => $user->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function findByEmail(Email $email): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => (string) $email]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapRowToUser($data);
    }

    public function findById(UserId $id): ?User
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute([':id' => (string) $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapRowToUser($data);
    }

    private function mapRowToUser(array $row): User
    {
        // Reflection or a reconstruct method might be needed if constructor enforces logic that we want to bypass when hydrating from DB.
        // For now, we assume constructor is safe or we use a static reconstruct method.
        // But User constructor generates createdAt if not passed.
        // Let's add a reconstruct method to User or just use reflection here.
        // For simplicity in this MVP, I'll use reflection to set properties if needed, or just new User(...) if it fits.
        // User constructor: __construct(UserId $id, Email $email, Password $password, Role $role)
        // It sets createdAt to now(). We need to restore original createdAt.

        $user = new User(
            UserId::fromString($row['id']),
            new Email($row['email']),
            Password::fromHash($row['password_hash']),
            Role::from($row['role'])
        );

        // Reflection to set createdAt
        $reflector = new \ReflectionClass($user);
        $property = $reflector->getProperty('createdAt');
        $property->setAccessible(true);
        $property->setValue($user, new \DateTimeImmutable($row['created_at']));

        return $user;
    }
}
