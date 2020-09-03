<?php

declare(strict_types=1);

/*
 * East Paas.
 *
 * LICENSE
 *
 * This source file is subject to the MIT license and the version 3 of the GPL3
 * license that are bundled with this package in the folder licences
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to richarddeloge@gmail.com so we can send you a copy immediately.
 *
 *
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
 *
 * @link        http://teknoo.software/east/paas Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace App;

use DI\Bridge\Symfony\Kernel as BaseKernel;
use DI\ContainerBuilder as DIContainerBuilder;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder as SfContainerBuilder;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use function DI\get;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }
    
    protected function buildPHPDIContainer(DIContainerBuilder $builder)
    {
        // Configure your container here
        $rootPath = dirname(__DIR__);
        $vendorPath = $rootPath . '/vendor';
        $builder->addDefinitions($vendorPath . '/teknoo/east-foundation/src/di.php');
        $builder->addDefinitions(
            $vendorPath . '/teknoo/east-foundation/infrastructures/symfony/Resources/config/di.php'
        );
        $builder->addDefinitions($vendorPath . '/teknoo/east-website/src/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-website/infrastructures/doctrine/di.php');
        $builder->addDefinitions(
            $vendorPath . '/teknoo/east-website/infrastructures/symfony/Resources/config/di.php'
        );
        $builder->addDefinitions($vendorPath . '/teknoo/east-website/infrastructures/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/src/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Doctrine/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Flysystem/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Git/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Kubernetes/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Docker/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Composer/di.php');
        $builder->addDefinitions($vendorPath . '/teknoo/east-paas/infrastructures/Symfony/Components/di.php');
        $builder->addDefinitions($rootPath . '/config/di.php');
        $builder->addDefinitions([ObjectManager::class => get('doctrine_mongodb.odm.default_document_manager')]);

        $builder->addDefinitions([
            'teknoo.east.paas.worker.global_variables' => [
                'ROOT' => \dirname(__DIR__)
            ],
            ObjectManager::class => get('doctrine_mongodb.odm.default_document_manager'),
        ]);

        return $builder->build();
    }

    protected function configureContainer(SfContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        // Feel free to remove the "container.autowiring.strict_mode" parameter
        // if you are using symfony/dependency-injection 4.0+ as it's the default behavior
        $container->setParameter('container.autowiring.strict_mode', true);
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, 'glob');
    }
}
