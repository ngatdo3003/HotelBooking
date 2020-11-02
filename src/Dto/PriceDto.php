<?php


namespace App\Dto;


use App\Entity\RoomType;

class PriceDto
{
    private $price;
    private $room_type;
    private $start_date;
    private $end_date;


    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getRoomType(): ?RoomType
    {
        return $this->room_type;
    }

    public function setRoomType(?RoomType $room_type): self
    {
        $this->room_type = $room_type;

        return $this;
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->start_date;
    }

    public function setStartDate(\DateTimeInterface $start_date): void
    {
        $this->start_date = $start_date;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->end_date;
    }


    public function setEndDate(\DateTimeInterface $end_date): void
    {
        $this->end_date = $end_date;
    }


}