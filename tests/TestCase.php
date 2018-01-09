<?php

namespace Digia\Lumen\ContentfulSync\Tests;

use Laravel\Lumen\Application;

/**
 * Class TestCase
 * @package Digia\Lumen\ContentfulSync\Tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->app = app();
    }

}
