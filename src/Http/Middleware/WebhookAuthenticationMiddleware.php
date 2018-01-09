<?php

namespace Digia\Lumen\ContentfulSync\Http\Middleware;

use Illuminate\Http\Request;

/**
 * Class WebhookAuthenticationMiddleware
 * @package Digia\Lumen\ContentfulSync\Http\Middleware
 */
class WebhookAuthenticationMiddleware
{

    /**
     * @var string
     */
    private $expectedUsername;

    /**
     * @var string
     */
    private $expectedPassword;

    /**
     * WebhookAuthenticationMiddleware constructor.
     *
     * @param string $expectedUsername
     * @param string $expectedPassword
     */
    public function __construct(string $expectedUsername, string $expectedPassword)
    {
        $this->expectedUsername = $expectedUsername;
        $this->expectedPassword = $expectedPassword;
    }

    /**
     * @param Request  $request
     * @param \Closure $next
     *
     * @return mixed
     * @throws \InvalidArgumentException on authorization failure
     */
    public function handle(Request $request, \Closure $next)
    {
        if ($request->hasHeader('Authorization')) {
            if ($request->header('Authorization') === $this->getExpectedAuthorizationLine()) {
                return $next($request);
            }
        }

        throw new \InvalidArgumentException('Invalid webhook authentication');
    }

    /**
     * @return string
     */
    private function getExpectedAuthorizationLine(): string
    {
        return 'Basic ' . base64_encode($this->expectedUsername . ':' . $this->expectedPassword);
    }

}
