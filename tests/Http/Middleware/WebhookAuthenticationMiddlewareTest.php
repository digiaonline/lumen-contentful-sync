<?php

namespace Digia\Lumen\ContentfulSync\Tests\Http\Middleware;

use Digia\Lumen\ContentfulSync\Http\Middleware\WebhookAuthenticationMiddleware;
use Illuminate\Http\Request;
use Digia\Lumen\ContentfulSync\Tests\TestCase;

/**
 * Class WebhookAuthenticationTest
 * @package Digia\Lumen\ContentfulSync\Tests\Http\Middleware
 */
class WebhookAuthenticationMiddlewareTest extends TestCase
{

    /**
     * Tests that the middleware passes when the configuration is correct
     */
    public function testMiddlewarePasses()
    {
        $authorizationLine = 'Basic ' . base64_encode('foo:bar');

        $request = new Request();
        $request->headers->add([
            'Authorization' => $authorizationLine,
        ]);

        $middleware = new WebhookAuthenticationMiddleware('foo', 'bar');
        $this->assertTrue($middleware->handle($request, function () {
            return true;
        }));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testMiddlewareFails()
    {
        $request = new Request();
        $request->headers->add([
            'Authorization' => 'You shall not pass!',
        ]);

        $middleware = new WebhookAuthenticationMiddleware('foo', 'bar');
        $middleware->handle($request, function () {

        });
    }

}
