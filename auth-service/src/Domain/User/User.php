<?php

namespace Trekly\Auth\Domain\User;

class User
{
    private UserId $id;
    private Email $email;
    private Password $password;
    private Role $role;
    private \DateTimeImmutable $createdAt;

    public function __construct(UserId $id, Email $email, Password $password, Role $role)
    {
        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->createdAt = new \DateTimeImmutable();
    }

    public static function create(Email $email, Password $password, Role $role): self
    {
        return new self(UserId::random(), $email, $password, $role);
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): Password
    {
        return $this->password;
    }

    public function getRole(): Role
    {
        return $this->role;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
