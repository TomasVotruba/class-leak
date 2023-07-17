<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver;

use Iterator;
use Symplify\PackageBuilder\Testing\AbstractKernelTestCase;
use TomasVotruba\ClassLeak\Kernel\EasyCIKernel;
use TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver\Source\FirstUsedClass;
use TomasVotruba\ClassLeak\Tests\ActiveClass\UseImportsResolver\Source\SecondUsedClass;
use TomasVotruba\ClassLeak\UseImportsResolver;

final class UseImportsResolverTest extends AbstractKernelTestCase
{
    private UseImportsResolver $useImportsResolver;

    protected function setUp(): void
    {
        $this->bootKernel(EasyCIKernel::class);
        $this->useImportsResolver = $this->getService(UseImportsResolver::class);
    }

    /**
     * @dataProvider provideData()
     *
     * @param string[] $filePaths
     * @param string[] $expectedClassUsages
     */
    public function test(array $filePaths, array $expectedClassUsages): void
    {
        $resolvedClassUsages = $this->useImportsResolver->resolveFromFilePaths($filePaths);
        $this->assertSame($expectedClassUsages, $resolvedClassUsages);
    }

    public function provideData(): Iterator
    {
        yield [[__DIR__ . '/Fixture/FileUsingOtherClasses.php'], [FirstUsedClass::class, SecondUsedClass::class]];
    }
}
