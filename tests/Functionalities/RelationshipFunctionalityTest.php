<?php

use FreddieGar\Base\Constants\HttpMethod;
use Illuminate\Http\Response;

/**
 * Class RelationshipFunctionalityTest
 */
class RelationshipFunctionalityTest extends DBTestCase
{
    public function testRelationshipNotFound()
    {
        $relationship = 'failed';
        $this->json(HttpMethod::GET, $this->_route('users', 1, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
        $response = $this->responseWithErrors();
        $this->assertEquals(trans('exceptions.relationship_not_found', compact('relationship')), $response->title);
    }

    public function testRelationshipCreatedBy()
    {
        $relationship = 'created-by';
        $this->json(HttpMethod::GET, $this->_route('users', 2, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->responseWithData();
    }

    public function testRelationshipUpdatedBy()
    {
        $relationship = 'updatedBy';
        $this->json(HttpMethod::GET, $this->_route('users', 4, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->responseWithData();
    }
}
