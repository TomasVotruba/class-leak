<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Filtering;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ClassLeak202403\Webmozart\Assert\Assert;
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
        'ClassLeak202403\\Symfony\\Component\\Console\\Application',
        'ClassLeak202403\\Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
        'ClassLeak202403\\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController',
        // events
        'ClassLeak202403\\Symfony\\Component\\EventDispatcher\\EventSubscriberInterface',
        // kernel
        'ClassLeak202403\\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
        'ClassLeak202403\\Symfony\\Component\\HttpKernel\\KernelInterface',
        'ClassLeak202403\\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
        // console
        'ClassLeak202403\\Symfony\\Component\\Console\\Command\\Command',
        'ClassLeak202403\\Twig\\Extension\\ExtensionInterface',
        'ClassLeak202403\\PhpCsFixer\\Fixer\\FixerInterface',
        'ClassLeak202403\\PHPUnit\\Framework\\TestCase',
        'ClassLeak202403\\PHPStan\\Rules\\Rule',
        'ClassLeak202403\\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter',
        // laravel
        'ClassLeak202403\\Illuminate\\Support\\ServiceProvider',
        'ClassLeak202403\\Illuminate\\Foundation\\Http\\Kernel',
        'ClassLeak202403\\Illuminate\\Contracts\\Console\\Kernel',
        'ClassLeak202403\\Illuminate\\Routing\\Controller',
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
