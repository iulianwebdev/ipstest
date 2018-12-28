<?php 

namespace Tests\Traits;

use Illuminate\Foundation\Testing\TestResponse as Response;

trait HttpPost {

    public function httpPost(array $payload): Response
    {
        return $this->json('POST', $this->url, $payload);
    }
}
