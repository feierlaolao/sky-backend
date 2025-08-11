<?php

namespace App\Controller;

use App\Request\FileUploadRequest;
use Aws\S3\S3Client;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\PostMapping;
use League\Flysystem\Filesystem;
use function Hyperf\Support\env;

#[Controller('file')]
class FileController
{

    #[Inject]
    protected Filesystem $filesystem;

    #[PostMapping('upload')]
    public function upload(FileUploadRequest $request)
    {
        $data = $request->validated();
        $key = 'uploads/' . uniqid('', true) . '.' . $data['extension'];
        //获得s3的鉴权url
        $config = [
            'version' => 'latest',
            'credentials' => [
                'key' => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
            ],
            'region' => env('S3_REGION'),
            'endpoint' => env('S3_ENDPOINT'),
            'use_path_style_endpoint' => true,
        ];
        $bucket = env('S3_BUCKET');
        $s3Client = new S3Client($config);

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');
        return $request->getUri();
    }

}