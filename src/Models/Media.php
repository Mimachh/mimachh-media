<?php

namespace Mimachh\Media\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'imageable_type',
        'imageable_uuid',
        'imageable_id',
        'name',
        'path',
        'mime',
        'size',
        'created_by',
        'created_at',
        'moderate_by',
        'moderate_at',
        'moderate_reason',
        'is_active',
    ];



    public function imageable()
    {
        return $this->morphTo(null, 'imageable_type', 'imageable_uuid', 'imageable_id');
    }


    /**
     * Create a new media instance from an uploaded file.
     *
     * @param \Illuminate\Http\UploadedFile $file The uploaded file instance.
     * @param \Illuminate\Database\Eloquent\Model $imageable The model associated with the media.
     * @param int|null $userId The ID of the user creating the media. Defaults to the currently authenticated user.
     * @param bool $isActive Whether the media is active. Defaults to true.
     * @param bool $isPrivate Whether the media should be stored in private storage. Defaults to false.
     * @param string|null $path The custom storage path for the media. If null, defaults to 'uploads/media'.
     * @return \Mimachh\Media\Models\Media The newly created media instance.
     */
    public static function createMedia(
        UploadedFile $file,
        $imageable,
        int $userId = null,
        bool $isActive = true,
        bool $isPrivate = false,
        string $path = null
    ) {
        // Generate a unique filename
        $uniqueFilename = uniqid() . '_' . $file->getClientOriginalName();

        // Determine the storage path
        $storagePath = $path ?? ($isPrivate ? 'private/uploads/media' : 'uploads/media');

        // Store the file in the determined path with the unique filename
        $storedPath = $file->storeAs($storagePath, $uniqueFilename, $isPrivate ? 'private' : 'public');

        return static::create([
            'name' => $file->getClientOriginalName(),
            'path' => $storedPath,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'created_by' => $userId ?? auth()->id(),
            'imageable_type' => get_class($imageable),
            'imageable_id' => $imageable->id,
            'is_active' => $isActive,
        ]);
    }

    /**
     * Update an existing media instance with new attributes.
     *
     * @param int $mediaId The ID of the media to update.
     * @param array $attributes The new attributes for the media.
     * @return \Mimachh\Media\Models\Media The updated media instance.
     */
    public static function updateMedia($mediaId, array $attributes)
    {
        $media = static::findOrFail($mediaId);
        $media->update($attributes);

        return $media;
    }


    /**
     * Moderate a media instance by updating moderation details.
     *
     * @param int $mediaId The ID of the media to moderate.
     * @param int $moderateBy The ID of the user performing the moderation.
     * @param string $moderateReason The reason for moderation.
     * @return \Mimachh\Media\Models\Media The moderated media instance.
     */
    public static function moderateMedia($mediaId, $moderateBy, $moderateReason)
    {
        $media = static::findOrFail($mediaId);
        $media->update([
            'moderate_by' => $moderateBy,
            'moderate_reason' => $moderateReason,
            'moderated_at' => now(),
        ]);

        return $media;
    }


    /**
     * Accessor pour générer automatiquement l'URL de stockage pour le champ 'path'.
     *
     * @return string
     */
    public function getPathAttribute($value)
    {
        // Résoudre l'URL en fonction du chemin de stockage
        return Storage::url($value);
    }


    // TODO : ajouter une méthode qui détecte si un média est dans la requête
    // $file = $request->file('media');

    // // Utiliser la méthode statique pour créer et associer le média
    // $media = Media::createMedia($file, $product);
    // $mediaUrls = $product->getMediaUrls();
}
