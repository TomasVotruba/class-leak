<?php

declare (strict_types=1);
namespace TomasVotruba\ClassLeak\Filtering;

use ClassLeak202412\Symfony\Component\Console\Helper\ProgressBar;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use ClassLeak202412\Webmozart\Assert\Assert;
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
        '\Symfony\\Component\\HttpKernel\\DependencyInjection\\Extension',
        '\Symfony\\Bundle\\FrameworkBundle\\Controller\\Controller',
        '\Symfony\\Bundle\\FrameworkBundle\\Controller\\AbstractController',
        '\Livewire\\Component',
        '\Illuminate\\Routing\\Controller',
        '\Illuminate\\Contracts\\Http\\Kernel',
        '\Illuminate\\Support\\ServiceProvider',
        // events
        '\Symfony\\Component\\EventDispatcher\\EventSubscriberInterface',
        // validator
        '\Symfony\\Component\\Validator\\Constraint',
        '\Symfony\\Component\\Validator\\ConstraintValidator',
        '\Symfony\\Component\\Validator\\ConstraintValidatorInterface',
        '\Symfony\\Component\\Security\\Core\\Authorization\\Voter\\VoterInterface',
        '\Symfony\\Component\\Security\\Http\\Logout\\LogoutSuccessHandlerInterface',
        '\Symfony\\Component\\Security\\Http\\Authentication\\AuthenticationSuccessHandlerInterface',
        '\Symfony\\Component\\Security\\Http\\Authorization\\AccessDeniedHandlerInterface',
        '\Symfony\\Component\\Security\\Http\\Authentication\\AuthenticationFailureHandlerInterface',
        // symfony forms
        '\Symfony\\Component\\ExpressionLanguage\\ExpressionFunctionProviderInterface',
        '\Symfony\\Component\\Form\\AbstractType',
        // doctrine
        '\Doctrine\\Common\\DataFixtures\\FixtureInterface',
        // kernel
        '\Symfony\\Component\\HttpKernel\\Bundle\\BundleInterface',
        '\Symfony\\Component\\HttpKernel\\KernelInterface',
        '\Symfony\\Component\\DependencyInjection\\Loader\\Configurator\\ContainerConfigurator',
        // console
        '\Symfony\\Component\\Console\\Command\\Command',
        '\Twig\\Extension\\ExtensionInterface',
        '\PhpCsFixer\\Fixer\\FixerInterface',
        '\PHPUnit\\Framework\\TestCase',
        '\PHPStan\\Rules\\Rule',
        '\PHPStan\\Command\\ErrorFormatter\\ErrorFormatter',
        // tests
        '\Behat\\Behat\\Context\\Context',
        // jms
        '\JMS\\Serializer\\Handler\\SubscribingHandlerInterface',
        // laravel
        '\Illuminate\\Support\\ServiceProvider',
        '\Illuminate\\Foundation\\Http\\Kernel',
        '\Illuminate\\Contracts\\Console\\Kernel',
        '\Illuminate\\Routing\\Controller',
        // Doctrine
        '\Doctrine\\Migrations\\AbstractMigration',
    ];
    /**
     * @var string[]
     */
    private const DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        '\Symfony\\Component\\Console\\Attribute\\AsCommand',
        '\Symfony\\Component\\HttpKernel\\Attribute\\AsController',
        '\Symfony\\Component\\EventDispatcher\\Attribute\\AsEventListener',
    ];
    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     *
     * @return FileWithClass[]
     */
    public function filter(array $filesWithClasses, array $usedClassNames, array $typesToSkip, array $suffixesToSkip, array $attributesToSkip, bool $shouldIncludeEntities, ?ProgressBar $progressBar) : array
    {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        Assert::allString($suffixesToSkip);
        $possiblyUnusedFilesWithClasses = [];
        $typesToSkip = \array_merge($typesToSkip, self::DEFAULT_TYPES_TO_SKIP);
        $attributesToSkip = \array_merge($attributesToSkip, self::DEFAULT_ATTRIBUTES_TO_SKIP);
        foreach ($filesWithClasses as $fileWithClass) {
            if ($progressBar instanceof ProgressBar) {
                $progressBar->advance();
            }
            if (\in_array($fileWithClass->getClassName(), $usedClassNames, \true)) {
                continue;
            }
            // is excluded interfaces?
            if ($this->shouldSkip($fileWithClass->getClassName(), $typesToSkip)) {
                continue;
            }
            if ($shouldIncludeEntities === \false && $fileWithClass->isEntity()) {
                continue;
            }
            if ($fileWithClass->isSerialized()) {
                continue;
            }
            // is excluded suffix?
            foreach ($suffixesToSkip as $suffixToSkip) {
                if (\substr_compare($fileWithClass->getClassName(), $suffixToSkip, -\strlen($suffixToSkip)) === 0) {
                    continue 2;
                }
            }
            // is excluded attributes?
            foreach ($fileWithClass->getAttributes() as $attribute) {
                if ($this->shouldSkip($attribute, $attributesToSkip)) {
                    continue 2;
                }
            }
            $possiblyUnusedFilesWithClasses[] = $fileWithClass;
        }
        return $possiblyUnusedFilesWithClasses;
    }
    /**
     * @param string[] $skips
     */
    private function shouldSkip(string $type, array $skips) : bool
    {
        foreach ($skips as $skip) {
            if (\strpos($type, '*') === \false && \is_a($type, $skip, \true)) {
                return \true;
            }
            if (\fnmatch($skip, $type, \FNM_NOESCAPE)) {
                return \true;
            }
        }
        return \false;
    }
}
