<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\EndToEnd;

use PHPUnit\Framework\TestCase;

final class CheckCommandEndToEndTest extends TestCase
{
    public function testSkipsClassWithClassLevelListenerAttribute(): void
    {
        $result = $this->runCheck(__DIR__ . '/Fixture/ClassLevelListener');

        $this->assertSame(0, $result['unused_class_count']);
    }

    public function testSkipsClassWithOwnMethodListenerAttribute(): void
    {
        $result = $this->runCheck(__DIR__ . '/Fixture/MethodLevelListener');

        $this->assertSame(0, $result['unused_class_count']);
    }

    public function testReportsClassWithOnlyInheritedListenerAttribute(): void
    {
        $result = $this->runCheck(__DIR__ . '/Fixture/InheritedListener');

        // the attribute lives on the parent, not on this class's own method, so it is not skipped
        $this->assertSame(1, $result['unused_class_count']);
        $this->assertStringContainsString('RealSubscriber', (string) json_encode($result));
    }

    public function testFailsOnShortAttributeName(): void
    {
        [$exitCode] = $this->runRaw(__DIR__ . '/Fixture/ClassLevelListener', ['--skip-attribute', 'AsEventListener']);

        $this->assertSame(1, $exitCode);
    }

    /**
     * @param string[] $extraArgs
     * @return array<string, mixed>
     */
    private function runCheck(string $path, array $extraArgs = []): array
    {
        [$exitCode, $stdout] = $this->runRaw($path, [...$extraArgs, '--json']);

        $result = json_decode($stdout, true);
        $this->assertIsArray($result, sprintf('Non-JSON output (exit %d): %s', $exitCode, $stdout));

        return $result;
    }

    /**
     * @param string[] $extraArgs
     * @return array{0: int, 1: string}
     */
    private function runRaw(string $path, array $extraArgs): array
    {
        $binary = __DIR__ . '/../../bin/class-leak';

        $command = escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($binary) . ' check ' . escapeshellarg($path);
        foreach ($extraArgs as $extraArg) {
            $command .= ' ' . escapeshellarg($extraArg);
        }

        $output = [];
        $exitCode = 0;
        exec($command, $output, $exitCode);

        return [$exitCode, implode("\n", $output)];
    }
}
