<?php

namespace App\Service;

use App\Entity\File;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileService
{
    public function __construct(private SluggerInterface $slugger, #[Autowire('%kernel.project_dir%/public/uploads/files')] private $filesDirectory)
    {}

    public function upload(UploadedFile $uploadedFile, File $file): File
    {
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$uploadedFile->guessExtension();

        try {
            $uploadedFile->move($this->filesDirectory, $newFilename);
        } catch (FileException $e) {
            throw new FileException($e);
        }

        $file->setPath($newFilename);
        $file->setType($uploadedFile->getMimeType());

        return $file;
    }

    public function delete(File $file): void
    {
        $filesystem = new Filesystem();

        if ($filesystem->exists($this->filesDirectory.'/'.$file->getPath())) {
            $filesystem->remove($this->filesDirectory.'/'.$file->getPath());
        }
    }
}
