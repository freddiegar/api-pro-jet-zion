<?php

use FreddieGar\Base\Constants\HttpMethod;
use FreddieGar\Base\Middlewares\SupportedMediaTypeMiddleware;
use Illuminate\Http\Response;

class GeneralFunctionalityTest extends TestCase
{
    public function testNotFoundHttp()
    {
        $model = 'error';
        $this->json(HttpMethod::POST, $this->_route($model), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.not_found', compact('model')), $response->title);
    }

    public function testMethodNotAllowedHttp()
    {
        $this->json(HttpMethod::GET, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->title);
    }

    public function testUnsopportedMediaTypeHttp()
    {
        $media_type = SupportedMediaTypeMiddleware::MEDIA_TYPE_SUPPORTED;
        $this->json(HttpMethod::GET, $this->_route('login'), [], ['CONTENT_TYPE' => '']);
        $this->assertResponseStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.unsopported_media_type', compact('media_type')), $response->title);
    }

    public function testTooManyAttemptsHttp()
    {
        for ($i = 0; $i < 5; ++$i) {
            $this->json(HttpMethod::POST, $this->_route('login'), [], $this->headers());
            $response = $this->responseWithErrors();
            $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
            $this->assertEquals(trans('exceptions.validation'), $response->title);
        }

        $this->json(HttpMethod::POST, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_TOO_MANY_REQUESTS);
    }
}
