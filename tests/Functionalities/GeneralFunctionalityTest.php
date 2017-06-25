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
        $this->seeJsonStructure([
            'message'
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertNotEmpty($response->message);
        $this->assertEquals(trans('exceptions.not_found', compact('model')), $response->message);
    }

    public function testMethodNotAllowedHttp()
    {
        $this->json(HttpMethod::GET, $this->_route('login'), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_METHOD_NOT_ALLOWED);
        $this->seeJsonStructure([
            'message'
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertNotEmpty($response->message);
        $this->assertEquals(trans('exceptions.method_not_allowed'), $response->message);
    }

    public function testUnsopportedMediaTypeHttp()
    {
        $media_type = SupportedMediaTypeMiddleware::MEDIA_TYPE_SUPPORTED;
        $this->json(HttpMethod::GET, $this->_route('login'), [], ['CONTENT_TYPE' => '']);
        $this->assertResponseStatus(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
        $this->seeJsonStructure([
            'message'
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertNotEmpty($response->message);
        $this->assertEquals(trans('exceptions.unsopported_media_type', compact('media_type')), $response->message);
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
