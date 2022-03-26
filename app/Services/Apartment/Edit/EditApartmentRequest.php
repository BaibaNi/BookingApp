<?php
namespace App\Services\Apartment\Edit;

class EditApartmentRequest
{
    private ?int $id;
    private string $title;
    private string $address;
    private string $description;
    private float $price;

    public function __construct(int $id, string $title, string $address, string $description, float $price)
    {
        $this->id = $id;
        $this->title = $title;
        $this->address = $address;
        $this->description = $description;
        $this->price = $price;
          }

    public function getId(): int
    {
        return $this->id;
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

}