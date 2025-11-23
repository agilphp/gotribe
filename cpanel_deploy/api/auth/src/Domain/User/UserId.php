<?php

namespace Trekly\Auth\Domain\User;

class UserId
{
    private string $id;

    private function __construct(string $id)
    {
        $this->id = $id;
    }

    public static function random(): self
    {
        return new self(uniqid('', true));
    }

    public static function fromString(string $id): self
    {
        return new self($id);
    }

    public function __toString(): string
    {
        return $this->id;
    }
}
