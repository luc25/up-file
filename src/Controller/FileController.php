<?php

namespace App\Controller;

use App\Constants\Roles;
use App\Entity\File;
use App\Form\FileType;
use App\Service\AWSS3Service;
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
    public function __construct(private FileService $fileService, private AWSS3Service $awsS3Service) {}

    #[Route('/upload', name: 'upload', methods: ['GET', 'POST'])]
    public function upload(Request $request, EntityManagerInterface $em): Response
    {
        $file = new File();
        $file->setUser($this->getUser());

        $form = $this->createForm(FileType::class, $file);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form->get('file')->getData();

            if ($uploadedFile) {
                $this->fileService->upload($uploadedFile, $file);
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

        return $this->render('file/show.html.twig', ['file' => $this->awsS3Service->getFile($file), 'delete_form' => $deleteForm]);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'PUT'])]
    public function edit(Request $request, EntityManagerInterface $em, File $file): Response
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
                $this->fileService->upload($uploadedFile, $file);
            }

            $em->flush();

            return $this->redirectToRoute('file_show', ['id' => $file->getId()]);
        }

        return $this->render('file/upload.html.twig', ['form' => $form]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $em, File $file): Response
    {
        $user = $this->getUser();

        if ($user !== $file->getUser() && !in_array(Roles::ROLE_ADMIN, $user->getRoles())) {
            throw new AccessDeniedHttpException();
        }

        $this->awsS3Service->deleteFile($file->getPath());
        $em->remove($file);
        $em->flush();

        return $this->redirectToRoute('homepage', ['id' => $file->getId()]);
    }
}
