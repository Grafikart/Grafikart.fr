<?php

namespace App;

use App\Domain\Application\Compiler\PropertyChangeListenerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        date_default_timezone_set('Europe/Paris');
        parent::boot();

        return;
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new PropertyChangeListenerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, 1);
    }
}
