<?php

namespace App\Service;

use App\Model\FileAttachment;

class FileService
{
    public function addFile($data)
    {
        $fileAttachment = new FileAttachment();
        $fileAttachment->upload_user_id = $data['upload_user_id'];
        $fileAttachment->bucket = $data['bucket'];
        $fileAttachment->object_key = $data['object_key'];
        $fileAttachment->save();
        return $fileAttachment;
    }
}