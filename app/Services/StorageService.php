<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;use App\Provider;
use App\Photo;
use Intervention\Image\ImageManagerStatic;

class StorageService extends CoreService
{
    const FOLDER_FOTOS = 'photos';

    const THUMB_PHOTO_HEIGHT = 400;
    const THUMB_PHOTO_WIDTH = 400;

    const TYPE_PUBLIC = 'public';
    const TYPE_PRIVATE = 'private';

    public function loadAnotherAvatar(string $url)
    {
        $path = $this->downloadAndStore($url, self::FOLDER_FOTOS, self::TYPE_PUBLIC);
        $path_thumb = $this->saveThumbByPath($path);
        return compact('path', 'path_thumb');
    }

    public function downloadAndStore(string $url, string $folder, $type = self::TYPE_PUBLIC)
    {
        $info = pathinfo($url);
        $contents = file_get_contents($url);
        $file = '/tmp/' . $info['basename'];
        file_put_contents($file, $contents);
        $uploaded_file = new UploadedFile($file, $info['basename']);
        return Storage::putFile($folder, $uploaded_file, $type);
    }

    public function storeUserPhoto(UploadedFile $file)
    {
        $path = Storage::putFile(self::FOLDER_FOTOS, $file, self::TYPE_PUBLIC);
        $path_thumb = $this->saveThumbByPath($path);
        return compact('path', 'path_thumb');
    }

    public function saveThumbByPath(string $path)
    {
        $pathThumb = $path;
        $fullPath = storage_path('app/public/'.$path);
        $img = ImageManagerStatic::make($fullPath);
        $height = $img->height();
        $width = $img->width();
        if($height >= $width && $height > self::THUMB_PHOTO_HEIGHT) {
            $pathThumb = str_replace('.', '_thumb.',$path);
            $fullPathThumb = storage_path('app/public/'.$pathThumb);
            $img->resize(null, self::THUMB_PHOTO_HEIGHT, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($fullPathThumb);
        } elseif($width > $height && $width > self::THUMB_PHOTO_WIDTH) {
            $pathThumb = str_replace('.', '_thumb.',$path);
            $fullPathThumb = storage_path('app/public/'.$pathThumb);
            $img->resize(self::THUMB_PHOTO_WIDTH, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->save($fullPathThumb);

        }
        return $pathThumb;
    }

    public function deleteUserPhoto(Photo $photo)
    {
        $this->deleteFile($photo->path);
        $this->deleteFile($photo->path_thumb);
        return true;
    }

    private function deleteFile(string $path)
    {
        Storage::delete($path);
    }
}