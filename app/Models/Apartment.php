<?php
namespace App\Models;

class Apartment
{
    private string $title;
    private string $address;
    private string $description;
    private ?string $availableFrom;
    private string $availableUntil;
    private ?int $id;
    private int $userId;

    public function __construct(string $title, string $address, string $description,
                                ?string $availableFrom, string $availableUntil,
                                ?int $id, int $userId)
    {
        $this->title = $title;
        $this->address = $address;
        $this->description = $description;
        $this->availableFrom = $availableFrom;
        $this->availableUntil = $availableUntil;
        $this->id = $id;
        $this->userId = $userId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getAvailableFrom(): string
    {
        return $this->availableFrom;
    }

    public function getAvailableUntil(): string
    {
        return $this->availableUntil;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}