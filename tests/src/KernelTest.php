<?php

declare(strict_types=1);

/*
* @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
* @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace Tests;

use App\Kernel;
use PHPUnit\Framework\TestCase;

/**
 * @¢overs \App\Kernel
 */
class KernelTest extends TestCase
{
    public function buildKernel(): Kernel
    {
        return new Kernel('test', false);
    }

    public function testGetCacheDir()
    {
        self::assertIsString($this->buildKernel()->getCacheDir());
    }

    public function testGetLogDir()
    {
        self::assertIsString($this->buildKernel()->getLogDir());
    }

    public function testTegisterBundles()
    {
        foreach ($this->buildKernel()->registerBundles() as $bundle) {
            self::assertIsObject($bundle);
        }
    }
}
