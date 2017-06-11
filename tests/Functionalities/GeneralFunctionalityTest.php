<?php

use App\Constants\HttpMethod;
use Illuminate\Http\Response;

class GeneralFunctionalityTest extends TestCase
{
    public function testNotFoundHttp()
    {
        $this->json(HttpMethod::POST, 'http://localhost/api/v1/error', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testMethodNotAllowedHttp()
    {
        $this->json(HttpMethod::GET, 'http://localhost/api/v1/login', [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }
}
