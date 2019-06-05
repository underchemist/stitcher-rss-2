<?php declare(strict_types=1);

namespace App\Stitcher;

use App\User;
use Psr\Http\Message\RequestInterface;
use function GuzzleHttp\Psr7\parse_query;
use function GuzzleHttp\Psr7\build_query;

class ParametersMiddleware
{
    /** @var callable */
    protected $handler;

    /** @var ?User */
    protected $user;

    public function __construct(?User $user)
    {
        $this->user = $user;
    }

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
            'version' => 8.9,
            'mode' => 'iPhoneApp',
            'udid' => config('services.stitcher.device'),
        ];

        if ($this->user !== null) {
            $query += ['uid' => $this->user->stitcher_id];
        }

        $uri = $request->getUri()->withQuery(build_query($query));
        $request = $request->withUri($uri);

        // @todo add parameter encryption

        return $handler($request, $options);
    }
}
