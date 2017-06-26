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
        $this->seeJsonStructure([
            'message'
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertNotEmpty($response->message);
        $this->assertEquals(trans('exceptions.relationship_not_found', compact('relationship')), $response->message);
    }

    public function testRelationshipCreatedBy()
    {
        $relationship = 'createdBy';
        $this->json(HttpMethod::GET, $this->_route('users', 2, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'user',
            'createdBy',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertObjectHasAttribute('user', $response);
        $this->assertObjectHasAttribute('createdBy', $response);
    }

    public function testRelationshipUpdatedBy()
    {
        $relationship = 'updatedBy';
        $this->json(HttpMethod::GET, $this->_route('users', 4, $relationship), [], $this->headers());
        $this->assertResponseStatus(Response::HTTP_OK);
        $this->seeJsonStructure([
            'user',
            'updatedBy',
        ]);
        $response = json_decode($this->response->getContent());
        $this->assertObjectHasAttribute('user', $response);
        $this->assertObjectHasAttribute('updatedBy', $response);
    }
}
