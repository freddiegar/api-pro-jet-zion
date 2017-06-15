<?php

use FreddieGar\Base\Constants\HttpMethod;
use Illuminate\Http\Response;

class GeneralFunctionalityTest extends TestCase
{
    public function testNotFoundHttp()
    {
        $this->json(HttpMethod::POST, $this->_route('error'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testMethodNotAllowedHttp()
    {
        $this->json(HttpMethod::GET, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public function testUnsopportedMediaTypeHttp()
    {
        $this->json(HttpMethod::GET, $this->_route('login'), [], ['CONTENT_TYPE' => '']);
        $this->assertResponseStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }

    public function testTooManyAttemptsHttp()
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->json(HttpMethod::POST, $this->_route('login'), [], $this->headers());
            $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->json(HttpMethod::POST, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
