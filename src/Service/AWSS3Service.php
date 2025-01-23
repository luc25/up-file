<?php

namespace App\Service;

use App\Entity\File;
use Aws\S3\ObjectUploader;
use Aws\S3\S3Client;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class AWSS3Service
{
    private S3Client $s3Client;

    public function __construct(
        #[Autowire('%env(AWS_ACCESS_KEY_ID)%')] $accessKey,
        #[Autowire('%env(AWS_SECRET_ACCESS_KEY)%')] $secret,
        #[Autowire('%env(AWS_VERSION)%')] $version,
        #[Autowire('%env(AWS_REGION)%')] $region,
        #[Autowire('%env(AWS_BUCKET_NAME)%')] private $bucketName
    ) {
        $this->s3Client = new S3Client([
            'version' => $version,
            'region'  => $region,
            'credentials' => [
                'key' => $accessKey,
                'secret' => $secret,
            ]
        ]);
    }

    public function getFile(File $file): array
    {
        return [
            'entity' => $file,
            's3_file' => base64_encode($this->s3Client->getObject(['Bucket' => $this->bucketName, 'Key' => $file->getPath()])->get('Body'))
        ];
    }

    public function getFiles(array $files): array
    {
        $s3Files = [];

        foreach ($files as $file) {
            $s3Files[] = [
                'entity' => $file,
                's3_file' => base64_encode($this->s3Client->getObject(['Bucket' => $this->bucketName, 'Key' => $file->getPath()])->get('Body'))
            ];
        }

        return $s3Files;
    }

    public function upload(string $filePath, string $key): void
    {
        $file = fopen($filePath, 'rb');
        $uploader = new ObjectUploader($this->s3Client, $this->bucketName, $key, $file);
        $uploader->upload();

        fclose($file);
    }

    public function deleteFile(string $key): void
    {
        $this->s3Client->deleteObject(['Bucket' => $this->bucketName, 'Key' => $key]);
    }
}
