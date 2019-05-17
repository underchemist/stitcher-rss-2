<?php declare(strict_types=1);

namespace App\Stitcher;

use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\parse_query;
use function GuzzleHttp\Psr7\build_query;

class ParametersMiddleware
{
    /** @var callable */
    protected $handler;

    public function __invoke(callable $handler)
    {
        $this->handler = $handler;
        return [$this, 'request'];
    }

    public function request(RequestInterface $request, array $options)
    {
        $handler = $this->handler;

        $query = parse_query($request->getUri()->getQuery());

        $query += [
            'version' => 4.31,
            'mode' => 'android',
            'udid' => config('services.stitcher.device'),
        ];

        $uri = $request->getUri()->withQuery(build_query($query));
        $request = $request->withUri($uri);

        // @todo add parameter encryption

        return $handler($request, $options);
    }
}
