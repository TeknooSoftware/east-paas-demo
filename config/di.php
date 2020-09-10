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

namespace Teknoo\Paas\Config;

use App\Middleware\HostnameRedirectionMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\CurlMultiHandler;
use GuzzleHttp\HandlerStack;
use Teknoo\East\Paas\Infrastructures\Composer\ComposerHook;
use Teknoo\East\Paas\Infrastructures\Guzzle\AsyncResponse;
use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UriFactory;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Teknoo\East\Foundation\Recipe\RecipeInterface;
use Teknoo\East\Paas\Contracts\Hook\HooksCollectionInterface;

use function DI\create;
use function DI\decorate;
use function DI\env;
use function DI\get;

return [
    'app.paas.hostname' => env('WEBSITE_HOSTNAME', 'localhost'),
    'app.paas.job_root' => env('JOB_ROOT', \sys_get_temp_dir()),
    'app.http_client.verify_ssl' => env('HTTP_CLIENT_VERIFY_SSL', true),
    'app.http_client.timeout' => env('HTTP_CLIENT_TIMEOUT', 30),

    'teknoo.east.paas.worker.add_history_pattern' => function (ContainerInterface $container): string {
        return 'https://' . $container->get('app.paas.hostname') . '/project/{projectId}/environment/{envName}/job/{jobId}/log';
    },

    'teknoo.east.paas.worker.global_variables' => [
        'ROOT' => \dirname(__DIR__)
    ],

    HostnameRedirectionMiddleware::class => function (ContainerInterface $container): HostnameRedirectionMiddleware {
        return new HostnameRedirectionMiddleware($container->get('app.paas.hostname'));
    },

    RecipeInterface::class => decorate(function ($previous, ContainerInterface $container) {
        if ($previous instanceof RecipeInterface) {
            $previous = $previous->registerMiddleware(
                $container->get(HostnameRedirectionMiddleware::class),
                4
            );
        }

        return $previous;
    }),

    'teknoo.east.paas.conductor.images_library' => [
        'php-run-74' => [
            'build-name' => 'php-run',
            'tag' => '7.4',
            'path' => '/library/php-run/7.4/',
        ],
    ],

    'teknoo.east.paas.root_dir' => \dirname(__DIR__),
    'teknoo.east.paas.worker.tmp_dir' => get('app.paas.job_root'),

    HooksCollectionInterface::class => static function (ContainerInterface $container): HooksCollectionInterface {
        return new class ($container) implements HooksCollectionInterface {

            private ContainerInterface $container;

            public function __construct(ContainerInterface $container)
            {
                $this->container = $container;
            }

            public function getIterator(): \Traversable {
                yield 'composer' => $this->container->get(ComposerHook::class);
            }
        };
    },

    UriFactoryInterface::class => get(UriFactory::class),
    UriFactory::class => create(),

    ResponseFactoryInterface::class => get(ResponseFactory::class),
    ResponseFactory::class => create(),

    RequestFactoryInterface::class => get(RequestFactory::class),
    RequestFactory::class => create(),

    StreamFactoryInterface::class => get(StreamFactory::class),
    StreamFactory::class => create(),

    //Misc
    ClientInterface::class => static function (ContainerInterface $container): ClientInterface {

        $curl = new CurlMultiHandler();
        $handler = HandlerStack::create($curl);

        $client = new Client([
            'verify' => 'true' === $container->get('app.http_client.verify_ssl'),
            'timeout' => (int) $container->get('app.http_client.timeout'),
            'handler' => $handler,
        ]);

        return new class ($client, $curl) implements ClientInterface {
            private Client $client;

            private CurlMultiHandler $curl;

            public function __construct(Client $client, CurlMultiHandler $curl)
            {
                $this->client = $client;
                $this->curl = $curl;
            }

            public function sendRequest(RequestInterface $request): ResponseInterface
            {
                $promise = $this->client->sendAsync($request);
                $this->curl->tick();

                return new AsyncResponse($promise);
            }
        };
    },
];