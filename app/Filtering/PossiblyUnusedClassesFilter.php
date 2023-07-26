<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Filtering;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ClassLeak202307\Webmozart\Assert\Assert;
final class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const DEFAULT_TYPES_TO_SKIP = [
        // http-kernel
        '\Symfony\\Component\\Console\\Application',
        '\Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
        '\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController',
        // events
        '\Symfony\\Component\\EventDispatcher\\EventSubscriberInterface',
        // kernel
        '\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
        '\Symfony\\Component\\HttpKernel\\KernelInterface',
        '\Symplify\\SymplifyKernel\\Contract\\LightKernelInterface',
        '\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
        // console
        '\Symfony\\Component\\Console\\Command\\Command',
        '\Twig\\Extension\\ExtensionInterface',
        '\PhpCsFixer\\Fixer\\FixerInterface',
        '\PHPUnit\\Framework\\TestCase',
        '\PHPStan\\Rules\\Rule',
        '\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter',
        // laravel
        '\Illuminate\\Support\\ServiceProvider',
        '\Illuminate\\Foundation\\Http\\Kernel',
        '\Illuminate\\Contracts\\Console\\Kernel',
        '\Illuminate\\Routing\\Controller',
    ];
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     *
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedClassNames, array $typesToSkip) : array
    {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        $possiblyUnusedFilesWithClasses = [];
        $typesToSkip = \array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);
        foreach ($filesWithClasses as $fileWithClass) {
            if (\in_array($fileWithClass->getClassName(), $usedClassNames, \true)) {
                continue;
            }
            // is excluded interfaces?
            foreach ($typesToSkip as $typeToSkip) {
                if ($this->isClassSkipped($fileWithClass, $typeToSkip)) {
                    continue 2;
                }
            }
            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }
        return $possiblyUnusedFilesWithClasses;
    }
    private function isClassSkipped(FileWithClass $fileWithClass, string $typeToSkip) : bool
    {
        if (\strpos($typeToSkip, '*') === \false) {
            return \is_a($fileWithClass->getClassName(), $typeToSkip, \true);
        }
        // try fnmatch
        return \fnmatch($typeToSkip, $fileWithClass->getClassName(), \FNM_NOESCAPE);
    }
}
