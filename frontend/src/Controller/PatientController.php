<?php

// src/Controller/PatientController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class PatientController extends AbstractController {
    private $httpClient;
    private $baseApiUrl;

    public function __construct(HttpClientInterface $httpClient, string $baseApiUrl) {
        $this->httpClient = $httpClient;
        $this->baseApiUrl = $baseApiUrl;
    }

    #[Route('/', name: 'patients_list_default')]
    #[Route('/patients', name: 'patients_list')]
    public function patientsList(Request $request): Response {
        $searchName = $request->query->get('name');
        $searchDni = $request->query->get('dni');
        $page = $request->query->getInt('page', 1);

        $url = $this->baseApiUrl . 'patients';
        $queryParams = [];

        if ($searchName) {
            $queryParams['name'] = $searchName;
        }

        if ($searchDni) {
            $queryParams['dni'] = $searchDni;
        }

        if ($page > 1) {
            $queryParams['page'] = $page;
        }

        if ($queryParams) {
            $url .= '?' . http_build_query($queryParams);
        }
dump($queryParams);
        dump($url);
        $response = $this->httpClient->request('GET', $url);
        $data = $response->toArray();

        $patients = $data['hydra:member'] ?? [];
        $pagination = $this->parsePagination($data);

        return $this->render('patient/list.html.twig', [
            'patients' => $patients,
            'pagination' => $pagination,
            'currentPage' => $page,
        ]);
    }

    private function parsePagination(array $data): array {
        $pagination = [];
        if (isset($data['hydra:view'])) {
            $view = $data['hydra:view'];
            $pagination['first'] = $this->getPageFromUrl($view['hydra:first'] ?? null);
            $pagination['last'] = $this->getPageFromUrl($view['hydra:last'] ?? null);
            $pagination['next'] = $this->getPageFromUrl($view['hydra:next'] ?? null);
            $pagination['prev'] = $this->getPageFromUrl($view['hydra:prev'] ?? null);
        }
        return $pagination;
    }

    private function getPageFromUrl(?string $url): ?int {
        if ($url) {
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            return $query['page'] ?? null;
        }
        return null;
    }


    #[Route('/serum', name: 'serum_create')]
    public function serumCreate(Request $request): Response {
        // Check if the form was submitted
        if ($request->isMethod('POST')) {
            $name = $request->request->get('name');
            $dni = $request->request->get('dni');
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
                "dni" => $dni,
                "serums" => [
                    [
                        "extractionDate" => $date
                    ]
                ]
            ];

            // Post data to /api/patients
            $response = $this->httpClient->request('POST', $this->baseApiUrl . 'patients', [
                'json' => $payload
            ]);

            if ($response->getStatusCode() === 201) {
                return $this->redirectToRoute('patients_list');
            } else {
                return new Response('Error: Unable to create patient', 500);
            }
        }

        return $this->render('patient/serum_create.html.twig');
    }
}


