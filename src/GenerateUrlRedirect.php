<?php

namespace Milon\JigsawUrlShortener;

use TightenCo\Jigsaw\Jigsaw;

class GenerateUrlRedirect
{
    public function handle(Jigsaw $jigsaw)
    {
        $redirects = $jigsaw->getConfig()->urlRedirects;

        foreach ($redirects as $redirect) {
            $stub = __DIR__ . DIRECTORY_SEPARATOR . 'stubs/redirect.blade.php';
            $renderedTemplate = app('view')->file($stub, ['url' => $redirect->url])->render();

            $jigsaw->writeOutputFile($redirect->filename . DIRECTORY_SEPARATOR . 'index.html', $renderedTemplate);
        }
    }
}

