<?php

namespace RRZE\MJML;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Render
{
    private $httpRequest;

    protected $nodeBin;

    protected $mjmlBin;

    public function __construct(string $nodeBin, string $mjmlBin)
    {
        $this->nodeBin = $nodeBin;
        $this->mjmlBin = $mjmlBin;
        $this->httpRequest = new HttpRequest();
    }

    public function run(): void
    {
        $body = $this->httpRequest->body();
        $bodyObj = json_decode($body);
        $content = $bodyObj->mjml;

        echo $this->render($content);
    }

    protected function render(string $content): string
    {
        $args = [
            $this->nodeBin,
            $this->mjmlBin,
            '-i',
            '-s',
            '--config.beautify'
        ];

        $process = new Process($args);
        $process->setInput($content);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            throw new \RuntimeException('Unable to transpile MJML. Stack error: ' . $e->getMessage());
        }

        $html = $process->getOutput();

        return json_encode([
            'html' => $this->stripFirstComment($html)
        ]);
    }

    protected function stripFirstComment($html)
    {
        $pos = strpos($html, '<!--');
        $_pos = strpos($html, '-->');
        if ($pos === 0 && $_pos !== false) {
            $html = substr($html, $_pos + 3);
        }
        return $html;
    }
}
