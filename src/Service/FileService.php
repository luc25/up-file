<?php

namespace App\Service;

use App\Entity\File;
use App\Repository\FileRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(private SluggerInterface $slugger, private FileRepository $fileRepo, private AWSS3Service $awsS3Service) {}

    public function getList(): array
    {
        $files = $this->fileRepo->findBy([], [], 10, 0);

        return $this->awsS3Service->getFiles($files);
    }

    public function upload(UploadedFile $uploadedFile, File $file): File
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        try {
            $this->awsS3Service->upload($uploadedFile->getPathname(), $newFilename);
        } catch (FileException $e) {
            throw new FileException($e);
        }

        $file->setPath($newFilename);
        $file->setType($uploadedFile->getMimeType());

        return $file;
    }
}
