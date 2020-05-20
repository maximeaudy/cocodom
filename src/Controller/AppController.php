<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Type;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class AppController extends AbstractController
{
    /**
     * @Route("/", name="dashboard")
     */
    public function index()
    {
        /** @var Type[] $types */
        $types = $this->getDoctrine()->getRepository(Type::class)->findAll();
        return $this->render('app/index.html.twig', [
            'types' => $types,
        ]);
    }

    /**
     * @Route("/api/capteurs", name="capteurs")
     */
    public function getCapteurs()
    {
        $capteurs = $this->getDoctrine()->getRepository(Data::class)->arrayFindAll();

        return $this->json(['capteurs' => $capteurs]);
    }

}
