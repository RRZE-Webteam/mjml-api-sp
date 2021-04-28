<?php

namespace RRZE\MJML;

use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Render
{
    protected $nodeBin;

    protected $mjmlBin;

    protected $log;

    protected $error;

    public function __construct(string $nodeBin, string $mjmlBin)
    {
        $this->nodeBin = $nodeBin;
        $this->mjmlBin = $mjmlBin;

        $this->log = new Logger('RRZE-MJML');
        $this->log->pushHandler(new StreamHandler(LOG_DIR . '/error.log', Logger::ERROR));
    }

    public function run(): void
    {
        $request = Request::json();
        if (empty($request['mjml'])) {
            $error = 'Invalid JSON or wrong parameters.';
            $this->log->error($error);
            echo Response::json(400, [
                'error' => $error,
                'mjml' => '',
                'html' => ''
            ]);
        } else {
            $content = $request['mjml'];
            echo $this->getCache($content);
        }
    }

    protected function getCache(string $content)
    {
        $templateContentHash = md5($content);
        $input = CACHE_DIR . '/' . $templateContentHash . '.mjml';
        $output = CACHE_DIR . '/' . $templateContentHash . '.html';

        if (is_file($output)) {
            $html = $this->read($output);
            return Response::json(200, [
                'error' => '',
                'mjml' => $content,
                'html' => $this->stripFirstComment($html)
            ]);
        }

        $this->write($input, $content);

        $html = $this->render($input, $output);

        if ($html == '') {
            $error = 'Unknow error.';
            $this->log->error($error);
            return Response::json(500, [
                'error' => $error,
                'mjml' => $content,
                'html' => ''
            ]);
        }

        return Response::json(200, [
            'error' => '',
            'mjml' => $content,
            'html' => $this->stripFirstComment($html)
        ]);
    }

    protected function render(string $input, string $output): string
    {
        $args = [
            $this->nodeBin,
            $this->mjmlBin,
            $input,
            '-o',
            $output,
            '--config.minify'
        ];

        $process = new Process($args);

        $html = '';

        try {
            $process->mustRun();
        } catch (ProcessFailedException $e) {
            $this->log->error('Unable to transpile MJML.', ['Error' => $e->getMessage()]);
        }

        if (is_file($output)) {
            $html = $this->read($output);
        }

        return $html;
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

    protected function read(string $file): string
    {
        $content = @file_get_contents($file);
        if ($content === false) {
            $content = '';
            $this->log->error('Unable to read file ' . $file . '.', ['Error' => $this->getLastError()]);
        }

        return $content;
    }

    protected function write(string $file, string $content, ?int $mode = 0_666): void
    {
        if (@file_put_contents($file, $content) === false) {
            $this->log->error('Unable to write file ' . $file . '.', ['Error' => $this->getLastError()]);
        }
        if ($mode !== null && !@chmod($file, $mode)) {
            $this->log->error('Unable to chmod file ' . $file . '.', ['Error' => $this->getLastError()]);
        }
    }

    protected function getLastError(): string
    {
        return preg_replace('#^\w+\(.*?\): #', '', error_get_last()['message']);
    }
}
