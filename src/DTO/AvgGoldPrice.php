<?php

namespace App\DTO;

use Symfony\Component\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class AvgGoldPrice
{

    #[Assert\NotBlank]
    #[Assert\DateTime(DATE_ATOM, message: 'Date should be ISO 8061 format.')]
    #[Serializer\Groups(["POST", "GET"])]
    private string $from;

    #[Assert\NotBlank]
    #[Assert\DateTime(DATE_ATOM, message: 'Date should be ISO 8061 format.')]
    #[Serializer\Groups(["POST", "GET"])]
    private string $to;

    #[Serializer\Groups("GET")]
    #[Serializer\SerializedName('avg')]
    private ?float $price = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFrom(): string
    {
        return $this->from;
    }

    public function setFrom(string $from): self
    {

        $this->from = (new \DateTime($from))->format(DATE_ATOM);

        return $this;
    }

    public function getTo(): ?string
    {
        return $this->to;
    }

    public function setTo(string $to): self
    {
        $this->to = (new \DateTime($to))->format(DATE_ATOM);

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
