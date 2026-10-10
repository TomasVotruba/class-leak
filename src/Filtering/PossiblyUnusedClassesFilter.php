<?php

declare(strict_types=1);

namespace TomasVotruba\ClassLeak\Filtering;

use InvalidArgumentException;
use TomasVotruba\ClassLeak\ValueObject\FileWithClass;

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
        'Twig\Extension\AbstractExtension',
        'PhpCsFixer\Fixer\FixerInterface',
        'PHPUnit\Framework\TestCase',
        'Symfony\Bundle\FrameworkBundle\Test\KernelTestCase',
        'Symfony\Bundle\FrameworkBundle\Test\WebTestCase',
        'Symfony\Component\Form\Test\FormIntegrationTestCase',
        'Symfony\Component\Validator\Test\ConstraintValidatorTestCase',
        'Twig\Test\IntegrationTestCase',
        'PHPStan\Rules\Rule',
        'PHPStan\Command\ErrorFormatter\ErrorFormatter',
        // tests
        'Behat\Behat\Context\Context',
        // jms
        'JMS\Serializer\Handler\SubscribingHandlerInterface',
        'JMS\Serializer\EventDispatcher\EventSubscriberInterface',
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
        'Symfony\Component\Routing\Attribute\Route',
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
     * Entry points, called by the router or the test runner, never by other code.
     *
     * @var string[]
     */
    private const DEFAULT_SUFFIXES_TO_SKIP = ['Controller', 'Test', 'TestCase'];

    /**
     * @param FileWithClass[] $filesWithClasses
     * @param string[] $usedClassNames
     * @param string[] $typesToSkip
     * @param string[] $suffixesToSkip
     * @param string[] $attributesToSkip
     * @param string[] $constructorInjectedNames types injected as constructor parameters somewhere
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
        array $constructorInjectedNames = []
    ): array {
        $this->assertAllString($usedClassNames);
        $this->assertAllString($typesToSkip);
        $this->assertAllString($suffixesToSkip);

        $possiblyUnusedFilesWithClasses = [];

        $typesToSkip = [...$typesToSkip, ...self::DEFAULT_TYPES_TO_SKIP];
        $attributesToSkip = [...$attributesToSkip, ...self::DEFAULT_ATTRIBUTES_TO_SKIP];
        $suffixesToSkip = [...$suffixesToSkip, ...self::DEFAULT_SUFFIXES_TO_SKIP];

        $implementedInterfaceNames = $this->resolveImplementedInterfaceNames($filesWithClasses);
        $parentTypeNamesByClass = $this->resolveParentTypeNamesByClass($filesWithClasses);

        foreach ($filesWithClasses as $fileWithClass) {
            if (in_array($fileWithClass->getClassName(), $usedClassNames, true)) {
                continue;
            }

            // interface is implemented at least once, class is resolved through it
            if (in_array($fileWithClass->getClassName(), $implementedInterfaceNames, true)) {
                continue;
            }

            // is excluded interfaces?
            if ($this->shouldSkip($fileWithClass->getClassName(), $typesToSkip)) {
                continue;
            }

            // declared ancestor is excluded, works without autoloading the analyzed project
            if ($this->isDeclaredSubtypeOfAny($fileWithClass->getClassName(), $typesToSkip, $parentTypeNamesByClass)) {
                continue;
            }

            if ($shouldIncludeEntities === false && $fileWithClass->isEntity()) {
                continue;
            }

            if ($fileWithClass->isSerialized()) {
                continue;
            }

            // implemented interface is injected via constructor, class is resolved through it
            if ($this->isImplementedInterfaceConstructorInjected($fileWithClass, $constructorInjectedNames)) {
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
     * @param FileWithClass[] $filesWithClasses
     * @return string[] interface names implemented by at least one scanned class
     */
    private function resolveImplementedInterfaceNames(array $filesWithClasses): array
    {
        $implementedInterfaceNames = [];
        foreach ($filesWithClasses as $fileWithClass) {
            $implementedInterfaceNames = [...$implementedInterfaceNames, ...$fileWithClass->getInterfaceNames()];
        }

        return array_unique($implementedInterfaceNames);
    }

    /**
     * @param FileWithClass[] $filesWithClasses
     * @return array<string, string[]>
     */
    private function resolveParentTypeNamesByClass(array $filesWithClasses): array
    {
        $parentTypeNamesByClass = [];
        foreach ($filesWithClasses as $fileWithClass) {
            $parentTypeNamesByClass[$fileWithClass->getClassName()] = $fileWithClass->getParentTypeNames();
        }

        return $parentTypeNamesByClass;
    }

    /**
     * Walks declared parents and interfaces through scanned classes, the parent outside scanned paths is matched by name
     *
     * @param string[] $typesToSkip
     * @param array<string, string[]> $parentTypeNamesByClass
     */
    private function isDeclaredSubtypeOfAny(string $className, array $typesToSkip, array $parentTypeNamesByClass): bool
    {
        $visitedNames = [
            $className => true,
        ];
        $namesToVisit = $parentTypeNamesByClass[$className] ?? [];

        while ($namesToVisit !== []) {
            $name = array_pop($namesToVisit);
            if (isset($visitedNames[$name])) {
                continue;
            }

            $visitedNames[$name] = true;

            if (in_array($name, $typesToSkip, true)) {
                return true;
            }

            $namesToVisit = [...$namesToVisit, ...($parentTypeNamesByClass[$name] ?? [])];
        }

        return false;
    }

    /**
     * A class whose interface is type-hinted in some constructor is wired by the container through it.
     *
     * @param string[] $constructorInjectedNames
     */
    private function isImplementedInterfaceConstructorInjected(
        FileWithClass $fileWithClass,
        array $constructorInjectedNames
    ): bool {
        foreach ($fileWithClass->getInterfaceNames() as $interfaceName) {
            if (in_array($interfaceName, $constructorInjectedNames, true)) {
                return true;
            }
        }

        return false;
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

    /**
     * @param string[] $values
     */
    private function assertAllString(array $values): void
    {
        foreach ($values as $value) {
            if (! is_string($value)) {
                throw new InvalidArgumentException(sprintf(
                    'Expected an array of strings, "%s" given',
                    get_debug_type($value)
                ));
            }
        }
    }
}
