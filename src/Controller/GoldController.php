<?php

namespace App\Controller;

use App\DTO\AvgGoldPrice;
use App\Service\ErrorJsonResponse;
use App\Service\NBPSerializer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GoldController extends BaseController
{

    public function __construct(readonly private SerializerInterface $serializer,
                                readonly  private  NBPSerializer $NBPSerializer,
                                readonly private ValidatorInterface $validator
    )
    {
        parent::__construct($serializer, $validator);
    }

    #[Route('/api/gold', name: 'app_gold', methods: ["POST"])]
    public function index(Request $request): JsonResponse
    {
        $goldPrice = $this->serializer->deserialize(
            $request->getContent(),
            AvgGoldPrice::class,
            'json',
            ['groups' => ['POST']]
        );

        $errors = $this->validator->validate($goldPrice);
        if(count($errors) > 0){
            return $this->errorJsonResponse($errors, 400);
        }

        $goldPrice = $this->NBPSerializer->serializeGoldPrice($goldPrice);

        return new JsonResponse($this->serializer
            ->serialize($goldPrice, 'json', ['groups' => ['GET']]),
            json:true
        );


    }
}
