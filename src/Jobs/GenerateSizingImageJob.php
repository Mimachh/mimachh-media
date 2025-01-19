<?php

namespace Mimachh\Media\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Mimachh\Media\Models\Media;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class GenerateSizingImageJob implements ShouldQueue
{
  

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $model;
    protected $media;


    public function __construct($model, Media $media)
    {
        $this->model = $model;
        $this->media = $media;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $conversions = $this->model->getMediaConversions();
        foreach ($conversions as $name => $width) {
            $image = Image::make(Storage::path($this->media->path));
            $image->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            // FIXME : revoir le path
            $conversionPath = "conversions/{$name}_{$this->media->filename}";
            Storage::put($conversionPath, (string) $image->encode());
        }
    }
}
