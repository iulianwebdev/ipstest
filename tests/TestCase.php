<?php

namespace Tests;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    const UNAUTHORIZED = 401;
    const BAD_REQUEST = 400;
    const NOT_FOUND = 404;
    const OK = 200;
    
    use CreatesApplication;

    protected function disableExceptionHandling() {
        
      $this->app->instance(ExceptionHandler::class, new class {
        public function report(Exception $e) {
          
        }
        public function render($request, Exception $e) {
          throw $e;
        }
      });
    }
}
