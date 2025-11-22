<?php

namespace Trekly\User\Domain\Profile;

class CreatorProfile
{
    private string $type; // NATURAL or JURIDICAL
    private string $documentInfo;
    private ?string $companyName;
    private bool $verified;

    public function __construct(string $type, string $documentInfo, ?string $companyName = null, bool $verified = false)
    {
        $this->type = $type;
        $this->documentInfo = $documentInfo;
        $this->companyName = $companyName;
        $this->verified = $verified;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getDocumentInfo(): string
    {
        return $this->documentInfo;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function isVerified(): bool
    {
        return $this->verified;
    }
}
