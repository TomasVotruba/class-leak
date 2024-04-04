<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\Functional;

use Iterator;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Console\Tester\CommandTester;
use TomasVotruba\ClassLeak\Commands\CheckCommand;
use TomasVotruba\ClassLeak\Tests\AbstractTestCase;
use TomasVotruba\ClassLeak\Tests\Functional\Fixture\Fixture1\MyResponse;

final class CheckCommandTest extends AbstractTestCase
{
    private CheckCommand $checkCommand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->checkCommand = $this->make(CheckCommand::class);
    }

    /**
     * @param array<mixed> $expectedResult
     */
    #[DataProvider('provideData')]
    public function test(string $path, array $expectedResult): void
    {
        $commandTester = new CommandTester($this->checkCommand);

        $commandTester->execute([
            'paths' => [$path],
            '--json' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();

        $json = $commandTester->getDisplay();

        $this->assertJson($json);
        $this->assertEquals($expectedResult, json_decode($json, true, 512, JSON_THROW_ON_ERROR));
    }

    public static function provideData(): Iterator
    {
        yield [
            __DIR__ . '/Fixture/Fixture1',
            [
                'unused_class_count' => 1,
                'unused_parent_less_classes' => [
                    [
                        'file_path' => __DIR__ . '/Fixture/Fixture1/MyResponse.php',
                        'namespace' => 'TomasVotruba\\ClassLeak\\Tests\\Functional\\Fixture\\Fixture1',
                        'class' => MyResponse::class,
                        'has_parent_class_or_interface' => false,
                        'has_api_tag' => false,
                        'used_attributes' => [],
                        'imports' => [],
                    ],
                ],
                'unused_classes_with_parents' => [],
            ],
        ];
    }
}
