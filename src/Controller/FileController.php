<?php

namespace App\Controller;

use App\Constants\Roles;
use App\Entity\File;
use App\Form\FileType;
use App\Service\FileService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file', name: 'file_')]
class FileController extends AbstractController
{
    #[Route('/upload', name: 'upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, EntityManagerInterface $em, FileService $fileService): Response
    {
        $file = new File();
        $file->setUser($this->getUser());

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                $fileService->upload($uploadedFile, $file);
            }

            $em->persist($file);
            $em->flush();

            return $this->redirectToRoute('file_show', ['id' => $file->getId()]);
        }

        return $this->render('file/upload.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(File $file): Response
    {
        $user = $this->getUser();

        if ($user !== $file->getUser() && !in_array(Roles::ROLE_ADMIN, $user->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $deleteForm = $this->createFormBuilder()
            ->setAction($this->generateUrl('file_delete', ['id' => $file->getId()]))
            ->setMethod('DELETE')
            ->getForm();

        return $this->render('file/show.html.twig', ['file' => $file, 'delete_form' => $deleteForm]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, FileService $fileService, File $file): Response
    {
        $user = $this->getUser();

        if ($user !== $file->getUser() && !in_array(Roles::ROLE_ADMIN, $user->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $form = $this->createForm(FileType::class, $file, ['method' => Request::METHOD_PUT, 'file_required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                $fileService->upload($uploadedFile, $file);
            }

            $em->flush();

            return $this->redirectToRoute('file_show', ['id' => $file->getId()]);
        }

        return $this->render('file/upload.html.twig', ['form' => $form]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, FileService $fileService, File $file): Response
    {
        $user = $this->getUser();

        if ($user !== $file->getUser() && !in_array(Roles::ROLE_ADMIN, $user->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $em->remove($file);
        $em->flush();

        $fileService->delete($file);

        return $this->redirectToRoute('homepage', ['id' => $file->getId()]);
    }
}
