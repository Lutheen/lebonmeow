<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileUploader
{
    private $imgDirectory;
    private $staticDirectory;

    public function __construct($imgDirectory, $staticDirectory)
    {
        $this->imgDirectory = $imgDirectory;
        $this->staticDirectory = $staticDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $filename = uniqid().".".$file->guessExtension();

        try {
            $file->move($this->getImgDirectory(), $filename);
        } catch (FileException $e) {
            //throw $th;
        }
        
        return 'images/products/'.$filename;
    }

    public function update(UploadedFile $file, $oldFile)
    {
        $fileUrl = $this->upload($file);
        
        try {
            unlink($this->getStaticDirectory().$oldFile);
        } catch (\Throwable $th){
            // throw $th
        }

        return $fileUrl;
    }

    public function getImgDirectory()
    {
        return $this->imgDirectory;
    }

    public function getStaticDirectory()
    {
        return $this->staticDirectory;
    }
}