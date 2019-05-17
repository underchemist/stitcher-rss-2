<?php declare(strict_types=1);

namespace App\Stitcher;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

class Api extends Client
{
    public function __construct(array $config = [])
    {
        $config += [
            'base_uri' => config('services.stitcher.url'),
            'connect_timeout' => 2,
            'headers' => [
                'User-Agent' => 'Podcasts/0.2 Unofficial Stitcher RSS',
            ]
        ];

        if (!isset($config['handler'])) {
            $config['handler'] = HandlerStack::create();
            $config['handler']->unshift(new ParametersMiddleware($config));
        }

        parent::__construct($config);
    }
}
