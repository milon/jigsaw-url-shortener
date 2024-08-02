<?php

namespace Milon\JigsawUrlShortener;

use TightenCo\Jigsaw\Jigsaw;

class GenerateUrlRedirect
{
    public function handle(Jigsaw $jigsaw)
    {
        $redirects = $jigsaw->getConfig()->urlRedirects;

        foreach ($redirects as $redirect) {
            $stub = __DIR__ . '/stubs/redirect.blade.php';
            $renderedTemplate = app('view')->file($stub, ['url' => $redirect->url])->render();

            $jigsaw->writeOutputFile($redirect->filename . '/index.html', $renderedTemplate);
        }
    }
}

