<?php

namespace Mimachh\Media\Traits;

use Illuminate\Support\Facades\Storage;
use Mimachh\Media\Models\Media;
use Intervention\Image\Laravel\Facades\Image;

trait HasMedia
{
    public function medias()
    {
        return $this->morphMany(Media::class, 'imageable');
    }


    /**
     * Add media to the model
     *
     * @param array $attributes
     * @return mixed
     */
    public function addMedia(array $attributes)
    {
        return $this->medias()->create($attributes);
    }

    /**
     * Add multiple media to the model
     * 
     * @param array $medias
     * @return mixed
     */
    public function addMedias(array $medias)
    {
        return $this->medias()->createMany($medias);
    }

    /**
     * Get the media of the model
     *
     * @return mixed
     */
    public function getMedia()
    {
        return $this->medias()->get();
    }

    /**
     * Get the media of the model by type
     *
     * @param string $type
     * @return mixed
     */
    public function getMediaByType(string $type)
    {
        return $this->medias()->where('type', $type)->get();
    }

    /** 
     * Delete the media of the model
     * 
     * @param int $id
     */
    public function deleteMedia(int $id)
    {
        $media = $this->medias()->find($id);
    
        // Supprime le fichier du stockage si le média existe
        if ($media && Storage::exists($media->path)) {
            Storage::delete($media->path);
        }
    
        // Supprime l'enregistrement de la base de données
        return $this->medias()->where('id', $id)->delete();
    }



    /** 
     * Delete all the media of the model
     * 
     */
    public function removeAllMedia()
    {
        $this->medias->each(function ($media) {
            // Supprime le fichier du stockage si le média existe
            if (Storage::exists($media->path)) {
                Storage::delete($media->path);
            }
        });
    
        // Supprime tous les enregistrements de la base de données
        return $this->medias()->delete();
    }


    /**
     * Resolve the URL of the media
     */
    // Méthode pour récupérer les URLs des fichiers média
    public function getMediaUrls()
    {
        return $this->medias->map(function ($media) {
            return Storage::url($media->path);
        });
    }

    // il faudra ajouter à la methode qui crée les images : 
    // if (method_exists($this, 'getMediaConversions')) {
    //     ProcessMediaConversions::dispatch($this, $media);
    // }

        /**
     * Define the media conversions for the model.
     *
     * @return array
     */
    // public function getMediaConversions(): array
    // {
    //     return [
    //         'thumbnail' => 150, // Crée une miniature de 150px de largeur
    //         'medium' => 300,    // Crée une image de taille moyenne de 300px de largeur
    //         'large' => 600,     // Crée une grande image de 600px de largeur
    //     ];
    // }
}
