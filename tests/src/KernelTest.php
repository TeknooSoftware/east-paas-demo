<?php

declare(strict_types=1);

/*
 * @copyright   Copyright (c) 2009-2020 Richard Déloge (richarddeloge@gmail.com)
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
