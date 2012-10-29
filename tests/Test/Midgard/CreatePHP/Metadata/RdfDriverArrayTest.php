<?php

namespace Test\Midgard\CreatePHP\Metadata;

use Midgard\CreatePHP\RdfMapperInterface;
use Midgard\CreatePHP\Metadata\RdfDriverArray;
use Midgard\CreatePHP\Entity\Controller;

class RdfDriverArrayTest extends RdfDriverBase
{
    /**
     * @var \Midgard\CreatePHP\Metadata\RdfDriverInterface
     */
    private $driver;

    public function setUp()
    {
        $def = array(
            "Test\\Midgard\\CreatePHP\\Model" => array (
               "vocabularies" => array(
                   "sioc" => "http://rdfs.org/sioc/ns#",
                   "dcterms" => "http://purl.org/dc/terms/",
               ),
               "typeof" => "sioc:Post",
               "config" => array(
                   "test" => "testvalue",
               ),
               "children" => array(
                   "title" => array(
                       "type" => "property",
                       "property" => "dcterms:title",
                       "tag-name" => "h2",
                   ),
                   "tags" => array(
                       "type" => "collection",
                       "rel" => "skos:related",
                       "tag-name" => "ul",
                       "config" => array(
                           "table" => "tags",
                       ),
                       "attributes" => array(
                           "class" => "tags",
                       )
                   ),
                   "children" => array(
                       "type" => "collection",
                       "rel" => "dcterms:hasPart",
                       "types" => array(
                           'sioc:Item',
                       ),
                   ),
                   "content" => array(
                       "type" => "property",
                       "property" => "sioc:content",
                   ),
               ),
            ),
            );
        $this->driver = new RdfDriverArray($def);
    }

    public function testLoadTypeForClass()
    {
        $mapper = $this->getMock('Midgard\\CreatePHP\\RdfMapperInterface');
        $typeFactory = $this->getMockBuilder('Midgard\\CreatePHP\\Metadata\\RdfTypeFactory')->disableOriginalConstructor()->getMock();
        $itemType = new Controller($mapper);
        $itemType->addRev('my:customRev');
        $typeFactory->expects($this->once())
            ->method('getType')
            ->with('http://rdfs.org/sioc/ns#Item')
            ->will($this->returnValue($itemType))
        ;

        $type = $this->driver->loadTypeForClass('Test\\Midgard\\CreatePHP\\Model', $mapper, $typeFactory);

        $this->assertTestNodetype($type);
    }

    /**
     * @expectedException Midgard\CreatePHP\Metadata\TypeNotFoundException
     */
    public function testLoadTypeForClassNodefinition()
    {
        $mapper = $this->getMock('Midgard\\CreatePHP\\RdfMapperInterface');
        $typeFactory = $this->getMockBuilder('Midgard\\CreatePHP\\Metadata\\RdfTypeFactory')->disableOriginalConstructor()->getMock();
        $type = $this->driver->loadTypeForClass('Midgard\\CreatePHP\\Not\\Existing\\Class', $mapper, $typeFactory);
    }

    /**
     * Gets the names of all classes known to this driver.
     *
     * @return array The names of all classes known to this driver.
     */
    public function testGetAllClassNames()
    {
        $map = $this->driver->getAllClassNames();
        $this->assertCount(1, $map);
        $types = array(
            'http://rdfs.org/sioc/ns#Post' => 'Test\\Midgard\\CreatePHP\\Model',
        );
        $this->assertEquals($types, $map);
    }
}
