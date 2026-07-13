<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use WeasyPrint\Contracts\WeasyPrint;
use WeasyPrint\Integration\Laravel\PDF as LaravelPDF;

abstract class PDF extends LaravelPDF
{
    public function saveToDisk(string $path): bool
    {
        $output = app(WeasyPrint::class)
            ->tapConfig($this->config(...))
            ->prepareSource($this->source())
            ->build();
        return Storage::disk('local')->put($path . DIRECTORY_SEPARATOR . $this->filename(), $output);
    }
}
