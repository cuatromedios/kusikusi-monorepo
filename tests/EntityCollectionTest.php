<?php

use Kusikusi\Extensions\EntityCollection;

class EntityCollectionTest extends TestCase
{

    private $collection = [
        [
            'id'=>'one',
            'properties' => [
                'format' => 'png',
                'isImage' => true,
                'isWebImage' => true
            ],
            'media_tags' => ['tag1', 'tag2']
        ],
        [
            'id'=>'two',
            'properties' => [
                'format' => 'tif',
                'isImage' => true,
                'isWebImage' => false
            ],
            'media_tags' => ['tag2', 'tag3']
        ],
        [
            'id'=>'three',
            'properties' => [
                'format' => 'jpg',
                'isImage' => true,
                'isWebImage' => true
            ],
            'media_tags' => ['tag3', 'tag4']
        ],
    ];

    public function testMediaWithTag()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediaWithTag('tag3');
        $this->assertEquals(2, $filtered->count());
        $this->assertEquals('two', $filtered->first()['id']);
    }
    public function testMediumWithTag()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediumWithTag('tag3');
        $this->assertEquals('two', $filtered['id']);
    }
    public function testMediaOfFormat()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediaOfFormat('jpg');
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals('three', $filtered->first()['id']);
    }
    public function testMediumOfFormat()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediumOfFormat('jpg');
        $this->assertEquals('three', $filtered['id']);
    }
    public function testWhereProperty()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->whereProperty('format', 'jpg');
        $this->assertEquals(1, $filtered->count());
        $this->assertEquals('three', $filtered->first()['id']);
    }
    public function testFirstWhereProperty()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->firstWhereProperty('format', 'jpg');
        $this->assertEquals('three', $filtered['id']);
    }
    public function testWhereIncludes()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->whereIncludes('media_tags', 'tag3');
        $this->assertEquals(2, $filtered->count());
        $this->assertEquals('two', $filtered->first()['id']);
    }
    public function testFirstWhereIncludes()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->firstWhereIncludes('media_tags', 'tag3');
        $this->assertEquals('two', $filtered['id']);
    }
    public function testMediaIsImage()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediaIsImage();
        $this->assertEquals(3, $filtered->count());
        $this->assertEquals('one', $filtered->first()['id']);
    }
    public function testMediumIsImage()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediumIsImage();
        $this->assertEquals('one', $filtered['id']);
    }
    public function testMediaIsWebImage()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediaIsWebImage();
        $this->assertEquals(2, $filtered->count());
        $this->assertEquals('one', $filtered->first()['id']);
    }
    public function testMediumIsWebImage()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediumIsWebImage();
        $this->assertEquals('one', $filtered['id']);
    }
    public function testMediaIsWebAudio()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediaIsWebAudio();
        $this->assertEquals(0, $filtered->count());
    }
    public function testMediumIsVideo()
    {
        $collection = EntityCollection::make($this->collection);
        $filtered = $collection->mediumIsVideo();
        $this->assertEquals(null, $filtered);
    }
}
