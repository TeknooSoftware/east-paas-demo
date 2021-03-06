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
* @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
* @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 *
 * @link        http://teknoo.software/east/paas Project website
 *
 * @license     http://teknoo.software/license/mit         MIT License
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Teknoo\East\Foundation\Http\ClientInterface;
use Teknoo\East\Foundation\Manager\ManagerInterface;
use Teknoo\East\Foundation\Middleware\MiddlewareInterface;
use Laminas\Diactoros\Response\RedirectResponse;

/**
* @copyright   Copyright (c) 2009-2021 EIRL Richard Déloge (richarddeloge@gmail.com)
* @copyright   Copyright (c) 2020-2021 SASU Teknoo Software (https://teknoo.software)
 * @author      Richard Déloge <richarddeloge@gmail.com>
 */
class HostnameRedirectionMiddleware implements MiddlewareInterface
{
    private string $allowedHost;

    public function __construct(string $allowedHost)
    {
        $this->allowedHost = $allowedHost;
    }

    public function execute(
        ClientInterface $client,
        ServerRequestInterface $request,
        ManagerInterface $manager
    ): MiddlewareInterface {
        $uri = $request->getUri();
        $hostname = $uri->getHost();

        if (\mb_strtolower($hostname) === \mb_strtolower($this->allowedHost)) {
            $manager->continueExecution($client, $request);

            return $this;
        }

        $uri = $uri->withHost($this->allowedHost);
        $redirectResponse = new RedirectResponse($uri, 302);

        $client->acceptResponse($redirectResponse);
        $manager->stop();

        return $this;
    }
}
