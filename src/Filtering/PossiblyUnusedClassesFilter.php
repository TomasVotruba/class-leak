<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Filtering;

use TomasVotruba\ClassLeak\ValueObject\FileWithClass;
use Webmozart\Assert\Assert;

final readonly class PossiblyUnusedClassesFilter
{
    /**
     * These class types are used by some kind of collector pattern. Either loaded magically, registered only in config,
     * an entry point or a tagged extensions.
     *
     * @var string[]
     */
    private const array DEFAULT_TYPES_TO_SKIP = [
        // http-kernel
        'Symfony\Component\Console\Application',
        'Symfony\Component\HttpKernel\DependencyInjection\Extension',
        'Symfony\Component\DependencyInjection\Extension\Extension',
        'Symfony\Bundle\FrameworkBundle\Controller\Controller',
        'Symfony\Bundle\FrameworkBundle\Controller\AbstractController',
        'Livewire\Component',
        'Illuminate\Routing\Controller',
        'Illuminate\Contracts\Http\Kernel',
        'Illuminate\Support\ServiceProvider',
        // events
        'Symfony\Component\EventDispatcher\EventSubscriberInterface',
        'Symfony\Component\Form\FormTypeExtensionInterface',
        'Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface',
        'Vich\UploaderBundle\Naming\DirectoryNamerInterface',
        // validator
        'Symfony\Component\Validator\Constraint',
        'Symfony\Component\Validator\ConstraintValidator',
        'Symfony\Component\Validator\ConstraintValidatorInterface',
        'Symfony\Component\Security\Core\Authorization\Voter\VoterInterface',
        'Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface',
        'Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface',
        'Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface',
        'Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface',

        // symfony forms
        'Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface',
        'Symfony\Component\Form\AbstractType',

        // doctrine
        'Doctrine\Common\DataFixtures\FixtureInterface',
        'Doctrine\Common\EventSubscriber',
        'Nelmio\Alice\ProcessorInterface',

        // kernel
        'Symfony\Component\HttpKernel\Bundle\BundleInterface',
        'Symfony\Component\HttpKernel\KernelInterface',
        'Symfony\Component\HttpKernel\HttpKernelInterface',
        'Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator',
        // console
        'Symfony\Component\Console\Command\Command',
        'Entropy\Console\Contract\CommandInterface',
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
    private const array DEFAULT_ATTRIBUTES_TO_SKIP = [
        // Symfony
        'Symfony\Component\Console\Attribute\AsCommand',
        'Symfony\Component\HttpKernel\Attribute\AsController',
        'Symfony\Component\EventDispatcher\Attribute\AsEventListener',
        'Symfony\Component\Messenger\Attribute\AsMessageHandler',
        // Doctrine
        'Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener',
        // Twig
        'Twig\Attribute\AsTwigFunction',
        'Twig\Attribute\AsTwigFilter',
        'Twig\Attribute\AsTwigTest',
    ];

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     * @param array<string, int> $usedClassNameCounts class name to number of files that reference it
     *
     * @return FileWithClass[]
     */
    public function filter(
        array $filesWithClasses,
        array $usedClassNames,
        array $typesToSkip,
        array $suffixesToSkip,
        array $attributesToSkip,
        bool $shouldIncludeEntities,
        array $usedClassNameCounts = [],
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

            if ($shouldIncludeEntities === false && $fileWithClass->isEntity()) {
                continue;
            }

            if ($fileWithClass->isSerialized()) {
                continue;
            }

            // implemented interface is injected/used elsewhere, class is resolved through it
            if ($this->isImplementedInterfaceUsedElsewhere($fileWithClass, $usedClassNameCounts)) {
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
     * The implementing class's own file references the interface once via its implements clause.
     * A count above one means another file references it, typically constructor injection by interface.
     *
     * @param array<string, int> $usedClassNameCounts
     */
    private function isImplementedInterfaceUsedElsewhere(FileWithClass $fileWithClass, array $usedClassNameCounts): bool
    {
        return array_any(
            $fileWithClass->getInterfaceNames(),
            fn (string $interfaceName): bool => ($usedClassNameCounts[$interfaceName] ?? 0) >= 2
        );
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
