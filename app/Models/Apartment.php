<?php
namespace App\Models;

class Apartment
{
    private string $title;
    private string $address;
    private string $description;
    private float $price;
    private ?string $availableFrom;
    private string $availableUntil;
    private ?int $id;
    private int $userId;
    private ?float $avgRating;

    public function __construct(string $title, string $address, string $description, float $price,
                                ?string $availableFrom, string $availableUntil,
                                ?int $id, int $userId, ?float $avgRating)
    {
        $this->title = $title;
        $this->address = $address;
        $this->description = $description;
        $this->price = $price;
        $this->availableFrom = $availableFrom;
        $this->availableUntil = $availableUntil;
        $this->id = $id;
        $this->userId = $userId;
        $this->avgRating = $avgRating;
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

    public function getPrice(): float
    {
        return $this->price;
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

    public function getAvgRating(): ?float
    {
        return $this->avgRating;
    }
}