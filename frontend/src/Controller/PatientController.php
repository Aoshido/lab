<?php

// src/Controller/PatientController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PatientController extends AbstractController
{
    private $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    #[Route('/patients', name: 'app_patients')]
    public function index(): Response
    {
        $response = $this->httpClient->request('GET', 'http://php/api/api/patients');
        $patients = $response->toArray()['hydra:member'];

        return $this->render('patient/index.html.twig', [
            'patients' => $patients,
        ]);
    }
}


