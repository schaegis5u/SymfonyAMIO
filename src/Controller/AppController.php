<?php

namespace App\Controller;

use App\Address\AddressApiInterface;
use Doctrine\Inflector\Rules\Transformation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class AppController extends AbstractController{
    /**
     * @Route("/")
     */
    public function home(): Response
    {
        return $this->render("app/home.html.twig", [
            'name' => 'Adolphe',
            'nationalité' => 'Belge',
        ]
    
        );
    }

    /**
     * @Route("search-address")
     */
    public function searchAddress(Request $request, AddressApiInterface $addressApiInterface): Response
    {
        $search = $request->get('search', '');
        $addresses = $addressApiInterface->searchAddress($search);

        return new JsonResponse([$addresses]);
    }
}

?>