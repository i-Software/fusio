<?php

namespace Fusio\Backend\Api\Routes;

use PSX\Test\ControllerDbTestCase;
use PSX\Test\Environment;

class EntityTest extends ControllerDbTestCase
{
    public function getDataSet()
    {
        return $this->createFlatXMLDataSet(__DIR__ . '/../../../fixture.xml');
    }

    public function testGet()
    {
        $response = $this->sendRequest('http://127.0.0.1/backend/routes/34', 'GET', array(
            'User-Agent'    => 'Fusio TestCase',
            'Authorization' => 'Bearer da250526d583edabca8ac2f99e37ee39aa02a3c076c0edc6929095e20ca18dcf'
        ));

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "id": 34,
    "methods": "GET",
    "path": "\/foo",
    "config": [
        {
            "active": true,
            "status": 4,
            "name": "1",
            "methods": [
                {
                    "active": true,
                    "public": true,
                    "name": "GET",
                    "action": 19,
                    "response": 1
                },
                {
                    "name": "POST"
                },
                {
                    "name": "PUT"
                },
                {
                    "name": "DELETE"
                }
            ]
        }
    ]
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);
    }

    public function testPost()
    {
        $response = $this->sendRequest('http://127.0.0.1/backend/routes/34', 'POST', array(
            'User-Agent'    => 'Fusio TestCase',
            'Authorization' => 'Bearer da250526d583edabca8ac2f99e37ee39aa02a3c076c0edc6929095e20ca18dcf'
        ), json_encode([
            'foo' => 'bar',
        ]));

        $body = (string) $response->getBody();

        $this->assertEquals(405, $response->getStatusCode(), $body);
    }

    public function testPut()
    {
        $response = $this->sendRequest('http://127.0.0.1/backend/routes/34', 'PUT', array(
            'User-Agent'    => 'Fusio TestCase',
            'Authorization' => 'Bearer da250526d583edabca8ac2f99e37ee39aa02a3c076c0edc6929095e20ca18dcf'
        ), json_encode([
            'methods' => 'GET|POST|PUT|DELETE',
            'path'    => '/foo',
            'config'  => [[
            	'status'  => 4,
            	'name'    => '1',
            	'methods' => [[
            		'name' => 'GET',
            	],[
            		'name' => 'POST',
            	],[
            		'name' => 'PUT',
            	],[
            		'name' => 'DELETE',
            	]],
            ]],
        ]));

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "success": true,
    "message": "Routes successful updated"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'status', 'methods', 'path', 'controller', 'config')
            ->from('fusio_routes')
            ->orderBy('id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getSQL();

        $row = Environment::getService('connection')->fetchAssoc($sql);

        $this->assertEquals(34, $row['id']);
        $this->assertEquals(1, $row['status']);
        $this->assertEquals('GET|POST|PUT|DELETE', $row['methods']);
        $this->assertEquals('/foo', $row['path']);
        $this->assertEquals('Fusio\Controller\SchemaApiController', $row['controller']);
        $this->assertEquals('a:1:{i:0;C:15:"PSX\Data\Record":517:{a:2:{s:4:"name";s:6:"config";s:6:"fields";a:3:{s:6:"status";i:4;s:4:"name";s:1:"1";s:7:"methods";a:4:{i:0;C:15:"PSX\Data\Record":70:{a:2:{s:4:"name";s:6:"method";s:6:"fields";a:1:{s:4:"name";s:3:"GET";}}}i:1;C:15:"PSX\Data\Record":71:{a:2:{s:4:"name";s:6:"method";s:6:"fields";a:1:{s:4:"name";s:4:"POST";}}}i:2;C:15:"PSX\Data\Record":70:{a:2:{s:4:"name";s:6:"method";s:6:"fields";a:1:{s:4:"name";s:3:"PUT";}}}i:3;C:15:"PSX\Data\Record":73:{a:2:{s:4:"name";s:6:"method";s:6:"fields";a:1:{s:4:"name";s:6:"DELETE";}}}}}}}}', $row['config']);
    }

    public function testDelete()
    {
        $response = $this->sendRequest('http://127.0.0.1/backend/routes/34', 'DELETE', array(
            'User-Agent'    => 'Fusio TestCase',
            'Authorization' => 'Bearer da250526d583edabca8ac2f99e37ee39aa02a3c076c0edc6929095e20ca18dcf'
        ));

        $body   = (string) $response->getBody();
        $expect = <<<'JSON'
{
    "success": true,
    "message": "Routes successful deleted"
}
JSON;

        $this->assertEquals(200, $response->getStatusCode(), $body);
        $this->assertJsonStringEqualsJsonString($expect, $body, $body);

        // check database
        $sql = Environment::getService('connection')->createQueryBuilder()
            ->select('id', 'status')
            ->from('fusio_routes')
            ->orderBy('id', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getSQL();

        $row = Environment::getService('connection')->fetchAssoc($sql);

        $this->assertEquals(34, $row['id']);
        $this->assertEquals(0, $row['status']);
    }
}