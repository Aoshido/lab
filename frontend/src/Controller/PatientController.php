<?php

// src/Controller/PatientController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/patients', name: 'patients_list')]
    public function patientsList(): Response
    {
        $response = $this->httpClient->request('GET', 'http://php/api/api/patients');
        $patients = $response->toArray()['hydra:member'];

        return $this->render('patient/index.html.twig', [
            'patients' => $patients,
        ]);
    }


    #[Route('/serum', name: 'serum_create')]
    public function serumCreate(Request $request): Response
    {
        // Check if the form was submitted
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $dateCheckbox = $request->request->get('custom_date');

            if ($dateCheckbox) {
                $dateString = $request->request->get('date');
                $dateTime = new \DateTime($dateString);
                $date = $dateTime->format('Y-m-d');
            } else {
                // Use the current date if no custom date is provided
                $date = (new \DateTime())->format('Y-m-d');
            }

            $payload = [
                "name" => $name,
                "serums" => [
                    [
                        "extractionDate" => $date
                    ]
                ]
            ];

            // Post data to /api/patients
            $response = $this->httpClient->request('POST', 'http://php/api/api/patients', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() === 201) {
                return $this->redirectToRoute('patients_list');
            } else {
                dump($response);die();
                return new Response('Error: Unable to create patient', 500);
            }
        }

        return $this->render('patient/serum_create.html.twig');
    }
}


