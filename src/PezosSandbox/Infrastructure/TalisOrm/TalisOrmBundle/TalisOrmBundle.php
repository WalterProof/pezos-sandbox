<?php

declare(strict_types=1);

namespace PezosSandbox\Infrastructure\TalisOrm\TalisOrmBundle;

use PezosSandbox\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\Compiler\SetAggregateClassesArgument;
use PezosSandbox\Infrastructure\TalisOrm\TalisOrmBundle\DependencyInjection\TalisOrmExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class TalisOrmBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new SetAggregateClassesArgument());
    }

    public function getContainerExtension()
    {
        return new TalisOrmExtension();
    }
}
