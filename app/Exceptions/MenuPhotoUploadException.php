<?php

namespace App\Exceptions;

use RuntimeException;

class MenuPhotoUploadException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Foto menu gagal disimpan ke storage.');
    }
}
