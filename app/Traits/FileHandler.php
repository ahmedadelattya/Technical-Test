<?php

namespace App\Traits;

use App\Enums\ContentTypeEnum;
use App\Enums\MediaTypeEnum;
use App\Models\Media;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

trait FileHandler
{
    /**
     * this function takes a base64 encoded image and store it in the filesystem and return the name of it
     * (ex. 12546735.png) that will be stored in DB
     * @param $file
     * @param $dir
     * @param false $is_base_64
     * @return string
     */
    public function storeFile($file, $dir): string
    {
        $this->makeDirectory(storage_path("app/public/$dir"));

        $mime = mime_content_type($file->getPathname());
        if (strstr($mime, "image/")) {
            $manager = new ImageManager(new Driver());

            $image = $manager->read($file);

            $size = 1200;


            $resizedName = $this->generateUniqueName($dir, "webp");

            // Check if the image width is greater than the target size
            if ($image->width() > $size) {
                // Resize if the image width exceeds the target size
                $image->scale(width: $size);
            }

            // Save the resized image as WebP
            $image->toWebp(quality: 100)->save(storage_path("app/public/$resizedName"));
        } else {
            $extension = pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
            $resizedName = $this->generateUniqueName($dir, $extension);
            $file->storeAs("public/", $resizedName);
        }

        return $resizedName;
    }

    private function generateUniqueName($dir, $extension)
    {
        return "$dir/" . str_replace([':', '\\', '/', '*'], '', bcrypt(microtime(true))) . ".$extension";
    }

    /**
     * this function takes image(DB name) and deletes it from the filesystem ,
     * returns true if deleted and false if not found
     * @param $file
     * @return bool
     */
    public function deleteFile($file): bool
    {
        if (file_exists(storage_path('app/public/') . $file)) {
            Storage::disk('public')->delete($file);
            return true;
        }
        return false;
    }

    /**
     * make directory for files
     * @param $path
     * @return mixed
     */
    private function makeDirectory($path): string
    {
        $this->files = new Filesystem();
        $this->files->makeDirectory($path, 0777, true, true);
        return $path;
    }



    public function StoreMediaToModel($model, $media, $type = MediaTypeEnum::FEATURED, $content_type = ContentTypeEnum::IMAGE): bool
    {
        if (is_array($media)) {
            foreach ($media as $one_media) {
                $file = $this->StoreMedia($one_media, $model);
                $model_media = new Media(['path' => $file, 'type' => $type, 'content_type' => $content_type]);
                $model->media()->save($model_media);
            }
        } else {
            $file = $this->StoreMedia($media, $model);
            $model_media = new Media(['path' => $file, 'type' => $type, 'content_type' => $content_type]);
            $model->media()->save($model_media);
        }
        return true;
    }

    public function ReplaceFeaturedMedia($model, $media, $type = MediaTypeEnum::FEATURED)
    {
        if ($model->media()->where('type', $type)->first())
            $this->DeleteMediaWithFile($model->media()->where('type', $type)->first());
        $file = $this->StoreMedia($media, $model);
        $model_media = new Media([$model->id, 'path' => $file, 'type' => $type, ' content_type' => ContentTypeEnum::IMAGE]);
        $model->media()->save($model_media);
    }
    public function ReplaceNotFeaturedMedia($model, $media, $type, $content_type)
    {
        $existingMedia = $model->media()
            ->where('type', $type)->first();
        if ($existingMedia) {
            $this->DeleteMediaWithFile($existingMedia);
        }
        $file = $this->StoreMedia($media, $model);
        $model_media = new Media([$model->id, 'path' => $file, 'type' => $type, 'content_type' => $content_type]);
        $model->media()->save($model_media);
    }

    private function StoreMedia($data, $model): string
    {
        $table = $model->getTable();
        $this->makeDirectory(storage_path('app/public/' . $table));

        $extension = $data->getClientOriginalExtension();
        $mime = $data->getMimeType();

        if ($mime === 'video/webm') {
            $extension = 'webm';
        }
        $filename = uniqid() . '.' . $extension;
        $path = $data->storeAs("public/{$table}", $filename);

        return str_replace('public/', '', $path);
    }


    public function deleteModelMultipleMedia($model)
    {
        foreach ($model->media as $media) {
            $this->DeleteMediaWithFile($media);
        }
        return true;
    }
    public function deleteModelMultipleMediaWithoutFeatured($model)
    {
        foreach ($model->media as $media) {
            if ($media->type != MediaTypeEnum::FEATURED) {
                $this->DeleteMediaWithFile($media);
            }
        }
        return true;
    }
    public function DeleteMediaWithFile($media): void
    {
        if (!(strpos($media->path, 'demo') !== false)) {
            if ($media->path)
                $this->deleteFile($media->path);
        }
        $media->delete();
    }

    public function DeleteMediaWithIds($model, array $deleted_media)
    {
        foreach ($deleted_media as $deleted_media_id) {
            $media = $model->media()->where('id', $deleted_media_id)->where('type', '!=', MediaTypeEnum::FEATURED)->first();

            if ($media) {

                $this->DeleteMediaWithFile($media);
            }
            $model->media()->where('id', $deleted_media_id)->where('type', '!=', MediaTypeEnum::FEATURED)->delete();
        }
    }
}
