<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use http\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/api/github",name="api_github_")
 */
class GithubController extends AbstractController
{
    /**
     *
     * Lists all Repos.
     * @Route("/list-repos", name="repos", methods={"GET"})
     *
     * @return Response
     */
    public function listRepos(Request $request, HttpClientInterface $client): Response
    {
        $queryParameters = $this->getQueryParameters($request);
        if (!$this->validateQueryParameters($queryParameters)) {
            return $this->json(['Not valid search parameters.']);
        }

        $response = $client->request('GET', $this->getParameter('GITHUB_REPOS_BASE_URL') . $this->getQueryParametersString($queryParameters));
        $content = $response->toArray();

        return $this->json($content);
    }

    protected function getQueryParameters(Request $request)
    {
        return [
            'created_at' => $request->get('created_at') ?? '2019-01-10',
            'sort' => $request->get('sort') ?? 'stars',
            'order' => $request->get('order') ?? 'desc',
            'per_page' => $request->get('per_page') ?? '10',
            'language' => $request->get('language') ?? 'javascript',
        ];
    }

    protected function validateQueryParameters($queryParameters)
    {
        $createdAt = $queryParameters['created_at'];
        $createdAtArr = explode('-', $createdAt);
        if (!count($createdAtArr) == 3) {
            return false;
        }
        if (!checkdate($createdAtArr[1], $createdAtArr[2], $createdAtArr[0])) {
            return false;
        }
        return true;
    }

    protected function getQueryParametersString($queryParameters)
    {
        $queryParametersString = "?q=created:%3E" . $queryParameters["created_at"];
        unset($queryParameters['created_at']);
        foreach ($queryParameters as $key => $value) {
            $queryParametersString .= "&$key=$value";
        }
        return $queryParametersString;
    }
}