<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $productDirectory;
    private $categoryDirectory;
    private $avatarDirectory;
    private $staticDirectory;

    public function __construct($productDirectory, $categoryDirectory, $avatarDirectory, $staticDirectory)
    {
        $this->productDirectory = $productDirectory;
        $this->categoryDirectory = $categoryDirectory;
        $this->avatarDirectory = $avatarDirectory;
        $this->staticDirectory = $staticDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $filename = md5(uniqid()).".".$file->guessExtension();

        try {
            $file->move($this->getProductDirectory(), $filename);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
        
        return 'images/products/'.$filename;
    }

    public function update(UploadedFile $file, $oldFile)
    {
        $fileUrl = $this->upload($file);
        
        try {
            unlink($this->getStaticDirectory().'/'.$oldFile);
        } catch (\Throwable $th){
            var_dump($th->getMessage());
        }

        return $fileUrl;
    }

    public function uploadCategory(UploadedFile $file)
    {
        $filename = md5(uniqid()).".".$file->guessExtension();

        try {
            $file->move($this->getCategoryDirectory(), $filename);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
        
        return 'images/categories/'.$filename;
    }

    public function updateCategory(UploadedFile $file, $oldFile)
    {
        $fileUrl = $this->uploadCategory($file);
        
        try {
            unlink($this->getStaticDirectory().'/'.$oldFile);
        } catch (\Throwable $th){
            var_dump($th->getMessage());
        }

        return $fileUrl;
    }

    public function uploadAvatar(UploadedFile $file)
    {
        $filename = md5(uniqid()).".".$file->guessExtension();

        try {
            $file->move($this->getAvatarDirectory(), $filename);
        } catch (\Throwable $th) {
            var_dump($th->getMessage());
        }
        
        return 'images/avatars/'.$filename;
    }

    public function updateAvatar(UploadedFile $file, $oldFile)
    {
        $fileUrl = $this->uploadAvatar($file);
        
        try {
            unlink($this->getStaticDirectory().'/'.$oldFile);
        } catch (\Throwable $th){
            var_dump($th->getMessage());
        }

        return $fileUrl;
    }

    public function getProductDirectory()
    {
        return $this->productDirectory;
    }

    public function getCategoryDirectory()
    {
        return $this->categoryDirectory;
    }

    public function getAvatarDirectory()
    {
        return $this->avatarDirectory;
    }

    public function getStaticDirectory()
    {
        return $this->staticDirectory;
    }
}