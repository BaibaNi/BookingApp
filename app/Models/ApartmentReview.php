<?php
namespace App\Models;

class ApartmentReview
{
    private string $name;
    private string $surname;
    private string $review;
    private int $rating;
    private string $createdAt;
    private ?int $id;
    private int $apartmentId;


    public function __construct(string $name, string $surname, string $review, int $rating, string $createdAt, ?int $id, int $apartmentId)
    {
        $this->name = $name;
        $this->surname = $surname;
        $this->review = $review;
        $this->rating = $rating;
        $this->createdAt = $createdAt;
        $this->id = $id;
        $this->apartmentId = $apartmentId;

    }


    public function getName(): string
    {
        return $this->name;
    }


    public function getSurname(): string
    {
        return $this->surname;
    }


    public function getReview(): string
    {
        return $this->review;
    }


    public function getRating(): int
    {
        return $this->rating;
    }


    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }


    public function getId(): ?int
    {
        return $this->id;
    }


    public function getApartmentId(): int
    {
        return $this->apartmentId;
    }

}