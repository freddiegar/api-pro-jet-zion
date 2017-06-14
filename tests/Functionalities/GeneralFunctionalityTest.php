<?php

use App\Constants\HttpMethod;
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
}
