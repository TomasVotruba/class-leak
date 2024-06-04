<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Filtering;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use Webmozart\Assert\Assert;

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
        'Symfony\Component\Console\Application',
        'Symfony\Component\HttpKernel\DependencyInjection\Extension',
        'Symfony\Bundle\FrameworkBundle\Controller\Controller',
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'Livewire\Component',
        'Illuminate\Routing\Controller',
        // events
        'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        // kernel
        'Symfony\Component\HttpKernel\Bundle\BundleInterface',
        'Symfony\Component\HttpKernel\KernelInterface',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        // console
        'Symfony\Component\Console\Command\Command',
        'Twig\Extension\ExtensionInterface',
        'PhpCsFixer\Fixer\FixerInterface',
        'PHPUnit\Framework\TestCase',
        'PHPStan\Rules\Rule',
        'PHPStan\Command\ErrorFormatter\ErrorFormatter',
        // tests
        'Behat\Behat\Context\Context',
        // jms
        'JMS\Serializer\Handler\SubscribingHandlerInterface',
        // laravel
        'Illuminate\Support\ServiceProvider',
        'Illuminate\Foundation\Http\Kernel',
        'Illuminate\Contracts\Console\Kernel',
        'Illuminate\Routing\Controller',
        // Doctrine
        'Doctrine\Migrations\AbstractMigration',
    ];

    /**
     * @var string[]
     */
    private const DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        'Symfony\Component\Console\Attribute\AsCommand',
        'Symfony\Component\HttpKernel\Attribute\AsController',
        'Symfony\Component\EventDispatcher\Attribute\AsEventListener',
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
    public function filter(
        array $filesWithClasses,
        array $usedClassNames,
        array $typesToSkip,
        array $suffixesToSkip,
        array $attributesToSkip
    ): array {
        Assert::allString($usedClassNames);
        Assert::allString($typesToSkip);
        Assert::allString($suffixesToSkip);

        $possiblyUnusedFilesWithClasses = [];

        $typesToSkip = [...$typesToSkip, ...self::DEFAULT_TYPES_TO_SKIP];
        $attributesToSkip = [...$attributesToSkip, ...self::DEFAULT_ATTRIBUTES_TO_SKIP];

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedClassNames, true)) {
                continue;
            }

            // is excluded interfaces?
            if ($this->shouldSkip($fileWithClass->getClassName(), $typesToSkip)) {
                continue;
            }

            // is excluded suffix?
            foreach ($suffixesToSkip as $suffixToSkip) {
                if (str_ends_with($fileWithClass->getClassName(), $suffixToSkip)) {
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
    private function shouldSkip(string $type, array $skips): bool
    {
        foreach ($skips as $skip) {
            if (! str_contains($type, '*') && is_a($type, $skip, true)) {
                return true;
            }

            if (fnmatch($skip, $type, FNM_NOESCAPE)) {
                return true;
            }
        }

        return false;
    }
}
