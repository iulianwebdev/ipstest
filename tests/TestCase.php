<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    const UNAUTHORIZED = 401;
    const BAD_REQUEST = 400;
    const NOT_FOUND = 404;
    const OK = 200;
    
    use CreatesApplication;
}
