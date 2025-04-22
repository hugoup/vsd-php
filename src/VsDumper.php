<?php

namespace HugoUp\Vsd;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class VsDumper
{
    private static ?VsDumper $instance = null;

    private function __construct() {}

    public static function getInstance(): VsDumper
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function dump(...$vars): void
    {
        $instance = self::getInstance();

        $output = $instance->generateDumpOutput($vars);
        $formattedOutput = $instance->formatDumpOutput($output);
        $backtrace = $instance->generateBacktrace();
        $finalOutput = $instance->prepareFinalOutput($formattedOutput, $backtrace);
        $instance->sendToSocket($finalOutput);
    }

    private function generateDumpOutput(array $vars): string
    {
        $cloner = new VarCloner();
        $dumper = new HtmlDumper();
        $output = fopen('php://memory', 'r+b');

        $dumper->dump($cloner->cloneVar($vars), $output);
        return stream_get_contents($output, -1, 0);
    }

    private function formatDumpOutput(string $output): string
    {
        $output = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $output);
        return preg_replace('#<style(.*?)>(.*?)</style>#is', '', $output);
    }

    private function generateBacktrace(): array
    {
        $backtrace = debug_backtrace();

        foreach (array_keys($backtrace) as $key) {
            foreach ($backtrace[$key]['args'] as $k => $a) {
                $backtrace[$key]['args'][$k] = gettype($a);
            }
        }

        // unset initial trace always inside dumper
        unset($backtrace[0]);
        unset($backtrace[1]);

        return array_values($backtrace);
    }

    private function prepareFinalOutput(string $dump, array $backtrace): array
    {
        $timeMarker = date('Y-m-d H:i:s');
        $current = $backtrace[0] ?? [];

        preg_match('/class=sf-dump id=(.*)\sdata/miU', $dump, $matches);
        $id = $matches[1] ?? null;

        return [
            'name' => $current['file'] . ':' . $current['line'],
            'id' => $id,
            'timestamp' => $timeMarker,
            'backtrace' => $backtrace,
            'headers' => $http_response_header ?? null, // @phpstan-ignore-line
            'request' => $_REQUEST,
            'output' => $dump,
        ];
    }

    private function sendToSocket(array $output): void
    {
        $host = getenv('VSD_HOST') ?: 'host.docker.internal';
        $socket = fsockopen($host, 9913);
        fwrite($socket, json_encode($output));
        fclose($socket);
    }
}
