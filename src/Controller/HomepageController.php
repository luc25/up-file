<?php

namespace App\Controller;

use App\Service\FileService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomepageController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function homepage(FileService $fileService): Response
    {
        return $this->render('homepage.html.twig', ['files' => $fileService->getList()]);
    }
}
