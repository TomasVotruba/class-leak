package filter

// defaultTypesToSkip are base types loaded by framework magic, config, or
// tagged collectors - classes of these types are never reported.
var defaultTypesToSkip = []string{
	// http-kernel
	`Symfony\Component\Console\Application`,
	`Symfony\Component\HttpKernel\DependencyInjection\Extension`,
	`Symfony\Component\DependencyInjection\Extension\Extension`,
	`Symfony\Bundle\FrameworkBundle\Controller\Controller`,
	`Symfony\Bundle\FrameworkBundle\Controller\AbstractController`,
	`Livewire\Component`,
	`Illuminate\Routing\Controller`,
	`Illuminate\Contracts\Http\Kernel`,
	`Illuminate\Support\ServiceProvider`,
	// events
	`Symfony\Component\EventDispatcher\EventSubscriberInterface`,
	`Symfony\Component\Form\FormTypeExtensionInterface`,
	`Symfony\Component\Security\Core\Authentication\SimpleAuthenticatorInterface`,
	`Vich\UploaderBundle\Naming\DirectoryNamerInterface`,
	// validator
	`Symfony\Component\Validator\Constraint`,
	`Symfony\Component\Validator\ConstraintValidator`,
	`Symfony\Component\Validator\ConstraintValidatorInterface`,
	`Symfony\Component\Security\Core\Authorization\Voter\VoterInterface`,
	`Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface`,
	`Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface`,
	`Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface`,
	`Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface`,
	// symfony forms
	`Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface`,
	`Symfony\Component\Form\AbstractType`,
	// doctrine
	`Doctrine\Common\DataFixtures\FixtureInterface`,
	`Doctrine\Common\EventSubscriber`,
	`Nelmio\Alice\ProcessorInterface`,
	// kernel
	`Symfony\Component\HttpKernel\Bundle\BundleInterface`,
	`Symfony\Component\HttpKernel\KernelInterface`,
	`Symfony\Component\HttpKernel\HttpKernelInterface`,
	`Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator`,
	// console
	`Symfony\Component\Console\Command\Command`,
	`Entropy\Console\Contract\CommandInterface`,
	`Twig\Extension\ExtensionInterface`,
	`PhpCsFixer\Fixer\FixerInterface`,
	`PHPUnit\Framework\TestCase`,
	`PHPStan\Rules\Rule`,
	`PHPStan\Command\ErrorFormatter\ErrorFormatter`,
	// tests
	`Behat\Behat\Context\Context`,
	// jms
	`JMS\Serializer\Handler\SubscribingHandlerInterface`,
	`JMS\Serializer\EventDispatcher\EventSubscriberInterface`,
	// laravel
	`Illuminate\Support\ServiceProvider`,
	`Illuminate\Foundation\Http\Kernel`,
	`Illuminate\Contracts\Console\Kernel`,
	`Illuminate\Routing\Controller`,
	// Doctrine
	`Doctrine\Migrations\AbstractMigration`,
}

// defaultAttributesToSkip are attributes marking a class as framework-collected.
var defaultAttributesToSkip = []string{
	// Symfony
	`Symfony\Component\Console\Attribute\AsCommand`,
	`Symfony\Component\HttpKernel\Attribute\AsController`,
	`Symfony\Component\Routing\Attribute\Route`,
	`Symfony\Component\EventDispatcher\Attribute\AsEventListener`,
	`Symfony\Component\Messenger\Attribute\AsMessageHandler`,
	// Doctrine
	`Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener`,
	// Twig
	`Twig\Attribute\AsTwigFunction`,
	`Twig\Attribute\AsTwigFilter`,
	`Twig\Attribute\AsTwigTest`,
}
