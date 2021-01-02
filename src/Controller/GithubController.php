<?php
// src/Controller/LuckyController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * @Route("/github",name="github_")
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
        $this->validateQueryParameters($queryParameters);

        try {
            $response = $client->request('GET', $this->getParameter('GITHUB_REPOS_BASE_URL') . $this->getQueryParametersString($queryParameters));
            $content = $response->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->render('github/list_repos.html.twig', [
            'repos' => $content['items']
        ]);
    }

    protected function getQueryParameters(Request $request)
    {
        return [
            'created_at' => $request->get('created_at') ?: '2019-01-10',
            'sort' => $request->get('sort') ?: 'stars',
            'order' => $request->get('order') ?: 'desc',
            'per_page' => $request->get('per_page') ?: '10',
            'language' => $request->get('language') ?: 'javascript',
        ];
    }

    protected function validateQueryParameters(&$queryParameters)
    {
        $createdAt = $queryParameters['created_at'];
        if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $createdAt)) {
            $queryParameters['created_at'] = '2019-01-10';
            $this->addFlash('error', 'Not valid date in created at (Y-m-d).');
            return false;
        }
    }

    protected function getQueryParametersString($queryParameters)
    {
        $queryParametersString = "?q=created:%3E" . $queryParameters["created_at"];
        $queryParametersString .= "+language:" . $queryParameters["language"];
        unset($queryParameters['created_at']);
        unset($queryParameters['language']);

        foreach ($queryParameters as $key => $value) {
            $queryParametersString .= "&$key=$value";
        }
        return $queryParametersString;
    }
}