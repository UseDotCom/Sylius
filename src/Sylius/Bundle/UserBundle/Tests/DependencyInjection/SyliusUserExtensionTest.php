<?php

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use PHPUnit\Framework\Assert;
use Sylius\Bundle\UserBundle\DependencyInjection\SyliusUserExtension;
use Sylius\Bundle\UserBundle\Factory\UserWithEncoderFactory;
use Sylius\Component\Resource\Factory\Factory;

final class SyliusUserExtensionTest extends AbstractExtensionTestCase
{
    /** @test */
    public function it_creates_default_resource_factory_by_default(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [],
                ],
            ],
        ]);

        $factoryDefinition = $this->container->getDefinition('sylius.factory.admin_user');

        Assert::assertSame(Factory::class, $factoryDefinition->getClass());
    }

    /** @test */
    public function it_decorates_user_factory_if_its_configuration_has_encoder_specified(): void
    {
        $this->load([
            'resources' => [
                'admin' => [
                    'user' => [
                        'encoder' => 'customencoder',
                    ],
                ],
            ],
        ]);

        $factoryDefinition = $this->container->getDefinition('sylius.factory.admin_user');

        Assert::assertSame(UserWithEncoderFactory::class, $factoryDefinition->getClass());
        Assert::assertSame('customencoder', $factoryDefinition->getArgument(1));
    }

    /** @test */
    public function it_decorates_user_factory_if_there_is_a_global_encoder_specified_in_the_configuration(): void
    {
        $this->load([
            'encoder' => 'customencoder',
            'resources' => [
                'admin' => [
                    'user' => [],
                ],
            ],
        ]);

        $factoryDefinition = $this->container->getDefinition('sylius.factory.admin_user');

        Assert::assertSame(UserWithEncoderFactory::class, $factoryDefinition->getClass());
        Assert::assertSame('customencoder', $factoryDefinition->getArgument(1));
    }

    /** @test */
    public function it_decorates_user_factory_using_the_most_specific_encoder_configured(): void
    {
        $this->load([
            'encoder' => 'customencoder',
            'resources' => [
                'admin' => [
                    'user' => [
                        'encoder' => 'evenmorecustomencoder',
                    ],
                ],
            ],
        ]);

        $factoryDefinition = $this->container->getDefinition('sylius.factory.admin_user');

        Assert::assertSame(UserWithEncoderFactory::class, $factoryDefinition->getClass());
        Assert::assertSame('evenmorecustomencoder', $factoryDefinition->getArgument(1));
    }

    protected function getContainerExtensions(): iterable
    {
        return [
            new SyliusUserExtension(),
        ];
    }
}