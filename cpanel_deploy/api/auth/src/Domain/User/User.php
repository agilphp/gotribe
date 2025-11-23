<?php

namespace Trekly\Auth\Domain\User;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 36)]
    private string $id;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $email;

    #[ORM\Column(name: 'password_hash', type: 'string', length: 255)]
    private string $passwordHash;

    #[ORM\Column(type: 'string', length: 50, enumType: Role::class)]
    private Role $role;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $id, string $email, string $passwordHash, Role $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->role = $role;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(string $email, string $password, Role $role): self
    {
        $id = uniqid('', true);
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);
        return new self($id, $email, $passwordHash, $role);
    }

    public function getId(): string { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function getRole(): Role { return $this->role; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    
    public function verifyPassword(string $password): bool
    {
        return password_verify($password, $this->passwordHash);
    }
}
