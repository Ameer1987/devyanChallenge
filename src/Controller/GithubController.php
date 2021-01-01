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
        $response = $client->request('GET', 'https://api.github.com/search/repositories?q=created:%3E2019-01-10&sort=stars&order=desc');

        $content = $response->toArray();
        return $this->json($content);
    }
}