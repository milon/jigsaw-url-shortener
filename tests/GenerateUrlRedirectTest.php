<?php

namespace Milon\JigsawUrlShortener\Tests;

use Illuminate\Container\Container;
use Milon\JigsawUrlShortener\GenerateUrlRedirect;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TightenCo\Jigsaw\Jigsaw;

class GenerateUrlRedirectTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    protected function setUp(): void
    {
        parent::setUp();

        Container::setInstance(new Container);
    }

    #[Test]
    public function it_writes_an_index_html_file_for_each_redirect(): void
    {
        $written = [];
        $jigsaw = $this->jigsawWithRedirects([
            (object) ['filename' => 'twitter', 'url' => 'https://twitter.com/to_milon'],
            (object) ['filename' => 'github', 'url' => 'https://github.com/milon'],
        ], $written);

        $this->bindViewRenderer();

        (new GenerateUrlRedirect)->handle($jigsaw);

        $this->assertArrayHasKey('twitter/index.html', $written);
        $this->assertArrayHasKey('github/index.html', $written);
        $this->assertStringContainsString('https://twitter.com/to_milon', $written['twitter/index.html']);
        $this->assertStringContainsString('https://github.com/milon', $written['github/index.html']);
        $this->assertStringContainsString('noindex', $written['twitter/index.html']);
        $this->assertStringContainsString('http-equiv="Refresh"', $written['twitter/index.html']);
    }

    #[Test]
    public function it_writes_nothing_when_there_are_no_redirects(): void
    {
        $written = [];
        $jigsaw = $this->jigsawWithRedirects([], $written);

        $this->bindViewRenderer();

        (new GenerateUrlRedirect)->handle($jigsaw);

        $this->assertSame([], $written);
    }

    #[Test]
    public function redirect_stub_contains_meta_refresh_and_url_placeholder(): void
    {
        $stub = file_get_contents(dirname(__DIR__) . '/src/stubs/redirect.blade.php');

        $this->assertNotFalse($stub);
        $this->assertStringContainsString('{{ $url }}', $stub);
        $this->assertStringContainsString('http-equiv="Refresh"', $stub);
        $this->assertStringContainsString('name="robots" content="noindex"', $stub);
        $this->assertStringContainsString('window.location.replace', $stub);
    }

    /**
     * @param  list<object{filename: string, url: string}>  $redirects
     * @param  array<string, string>  $written
     */
    private function jigsawWithRedirects(array $redirects, array &$written): Jigsaw
    {
        $jigsaw = Mockery::mock(Jigsaw::class);
        $jigsaw->shouldReceive('getConfig')->once()->andReturn((object) [
            'urlRedirects' => $redirects,
        ]);
        $jigsaw->shouldReceive('writeOutputFile')
            ->andReturnUsing(function (string $filename, string $contents) use (&$written) {
                $written[$filename] = $contents;

                return true;
            });

        return $jigsaw;
    }

    private function bindViewRenderer(): void
    {
        Container::getInstance()->instance('view', new class
        {
            public function file(string $path, array $data): object
            {
                return new class($path, $data)
                {
                    public function __construct(private string $path, private array $data)
                    {
                    }

                    public function render(): string
                    {
                        $stub = file_get_contents($this->path);

                        return str_replace('{{ $url }}', $this->data['url'], $stub);
                    }
                };
            }
        });
    }
}
