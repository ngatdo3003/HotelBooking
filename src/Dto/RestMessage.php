<?php


namespace App\Dto;

use DateTime;
use JsonSerializable;

class RestMessage{
    private $error;
    private $message;
    private $data;
    private $timestamp;

    /**
     * RestMessage constructor.
     * @param $error
     * @param $message
     * @param $data
     */
    public function __construct($error, $message, $data)
    {
        $this->error = $error;
        $this->message = $message;
        $this->data = $data;
        $this->timestamp = new DateTime();
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message): void
    {
        $this->message = $message;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp): void
    {
        $this->timestamp = $timestamp;
    }



}