<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\RoomAvailability;
use Doctrine\ORM\Mapping as ORM;

/**
 * @RoomAvailability
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $company;

    /**
     * @Assert\NotBlank
     * @Assert\DateTime
     * @Assert\LessThan(propertyPath="bookedTo", message="Start date is after End date")
     * @ORM\Column(type="datetime")
     */
    private $bookedFrom;

    /**
     * @Assert\NotBlank
     * @Assert\DateTime
     * @ORM\Column(type="datetime")
     */
    private $bookedTo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getBookedFrom(): ?\DateTimeInterface
    {
        return $this->bookedFrom;
    }

    public function setBookedFrom(\DateTimeInterface $bookedFrom): self
    {
        $this->bookedFrom = $bookedFrom;

        return $this;
    }

    public function getBookedTo(): ?\DateTimeInterface
    {
        return $this->bookedTo;
    }

    public function setBookedTo(\DateTimeInterface $bookedTo): self
    {
        $this->bookedTo = $bookedTo;

        return $this;
    }
}
