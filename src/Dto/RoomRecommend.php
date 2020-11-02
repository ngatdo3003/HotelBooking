<?php


namespace App\Dto;


class RoomRecommend
{
    private $id;
    private $name;
    private $room_type;
    private $price;

    /**
     * RoomRecommend constructor.
     * @param $id
     * @param $name
     * @param $room_type
     * @param $price
     */
    public function __construct($id, $name, $room_type, $price)
    {
        $this->id = $id;
        $this->name = $name;
        $this->room_type = $room_type;
        $this->price = $price;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getRoomType()
    {
        return $this->room_type;
    }

    /**
     * @param mixed $room_type
     */
    public function setRoomType($room_type): void
    {
        $this->room_type = $room_type;
    }

    /**
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param mixed $price
     */
    public function setPrice($price): void
    {
        $this->price = $price;
    }

}