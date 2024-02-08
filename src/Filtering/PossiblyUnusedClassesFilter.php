<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Filtering;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ClassLeak202402\Webmozart\Assert\Assert;
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
        'ClassLeak202402\\Symfony\\Component\\Console\\Application',
        'ClassLeak202402\\Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
        'ClassLeak202402\\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController',
        // events
        'ClassLeak202402\\Symfony\\Component\\EventDispatcher\\EventSubscriberInterface',
        // kernel
        'ClassLeak202402\\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
        'ClassLeak202402\\Symfony\\Component\\HttpKernel\\KernelInterface',
        'ClassLeak202402\\Symplify\\SymplifyKernel\\Contract\\LightKernelInterface',
        'ClassLeak202402\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
        // console
        'ClassLeak202402\\Symfony\\Component\\Console\\Command\\Command',
        'ClassLeak202402\\Twig\\Extension\\ExtensionInterface',
        'ClassLeak202402\\PhpCsFixer\\Fixer\\FixerInterface',
        'ClassLeak202402\\PHPUnit\\Framework\\TestCase',
        'ClassLeak202402\\PHPStan\\Rules\\Rule',
        'ClassLeak202402\\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter',
        // laravel
        'ClassLeak202402\\Illuminate\\Support\\ServiceProvider',
        'ClassLeak202402\\Illuminate\\Foundation\\Http\\Kernel',
        'ClassLeak202402\\Illuminate\\Contracts\\Console\\Kernel',
        'ClassLeak202402\\Illuminate\\Routing\\Controller',
    ];
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     *
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedClassNames, array $typesToSkip, array $suffixesToSkip) : array
    {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        Assert::allString($suffixesToSkip);
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
            // is excluded suffix?
            foreach ($suffixesToSkip as $suffixToSkip) {
                if (\substr_compare($fileWithClass->getClassName(), $suffixToSkip, -\strlen($suffixToSkip)) === 0) {
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
