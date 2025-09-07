<?php

namespace App\Controller;

use App\MyResponse;
use App\Request\FileUploadRequest;
use App\Service\FileService;
use Aws\S3\S3Client;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\GetMapping;
use League\Flysystem\Filesystem;
use function Hyperf\Support\env;

#[Controller('file')]
class FileController
{

    #[Inject]
    protected Filesystem $filesystem;

    #[Inject]
    protected FileService $fileService;

    #[GetMapping('upload')]
    public function upload(FileUploadRequest $request)
    {
        $data = $request->validated();
        $key = '/uploads/' . md5(uniqid('', true)) . '.' . $data['extension'];
        $bucket = env('S3_BUCKET');
        $fileAttachment = $this->fileService->addFile([
            'bucket' => $bucket,
            'upload_user_id' => '',
            'object_key' => $key,
        ]);
        //获得s3的鉴权url
        $config = [
            'version' => 'latest',
            'credentials' => [
                'key' => env('S3_KEY'),
                'secret' => env('S3_SECRET'),
            ],
            'region' => env('S3_REGION'),
            'endpoint' => env('S3_ENDPOINT'),
            'use_path_style_endpoint' => false,
            'bucket_endpoint' => true,
        ];

        $s3Client = new S3Client($config);

        $cmd = $s3Client->getCommand('PutObject', [
            'Bucket' => $bucket,
            'Key' => $key,
        ]);
        $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');
        return MyResponse::success([
            'id' => (string)$fileAttachment->id,
            'object_url' => env('S3_ENDPOINT').'/'.$key,
            'upload_url' => $request->getUri(),
        ])->toArray();
    }

}