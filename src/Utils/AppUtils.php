<?php


namespace App\Utils;


use App\Repository\UserRepository;
use FOS\RestBundle\View\ViewHandlerInterface;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AppUtils
{
    private $serializer;

    /**
     * @param SerializerInterface $serializer
     */
    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }
    public function getErrorMessages(ConstraintViolationListInterface $errors) {
       $errorMessage = "";
        foreach ($errors as $error){
            $errorMessage = $errorMessage.$error->getPropertyPath().": ";
            $errorMessage = $errorMessage.$error->getMessage()." ";
        }
        return $errorMessage;
    }

    public function toJson($obj) {
        return $this->serializer->serialize($obj, 'json');
    }
    public function toObj($json, $class){
        return $this->serializer->deserialize($json, $class, 'json');
    }
}