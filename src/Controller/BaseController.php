<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class BaseController extends AbstractController
{
    public function __construct(readonly private SerializerInterface $serializer,
                                readonly private ValidatorInterface $validator
    )
    {
    }

    public function errorJsonResponse(ConstraintViolationList $errors, int $statusCode): JsonResponse
    {
        $output = [];
        foreach ($errors->getIterator() as $error){
            /** @var ConstraintViolation $error */
            $temp['property'] = $error->getPropertyPath();
            $temp['message'] = $error->getMessage();
            $output[] =  $temp;
        }

        return new JsonResponse($output,  $statusCode);

    }

}