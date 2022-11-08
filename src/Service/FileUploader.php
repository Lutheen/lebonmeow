<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        // $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->getImgDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return 'images/products/'.$fileName;
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