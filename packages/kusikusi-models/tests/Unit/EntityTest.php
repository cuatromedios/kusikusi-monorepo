<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;

class EntityTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function an_entity_can_be_created()
  {
    $entity = Entity::factory()->create();
    $this->assertDatabaseCount((new Entity())->getTable(), 1);
    $this->assert_default_values($entity);
  }

  /** @test */
  function an_entity_can_be_saved()
  {
    $entity = new Entity();
    $entity->save();
    $this->assertDatabaseCount((new Entity())->getTable(), 1);
    $this->assert_default_values($entity);
  }

  /** @test */
  function properties_can_be_set()
  {
    $key1 = 'string';  $value1 = 'yes';
    $key2 = 'number';  $value2 = 1;
    $key3 = 'boolean'; $value3 = true;
    $entity = Entity::create(["properties" => [$key1 => $value1, $key2 => $value2, $key3 => $value3]]);
    $savedEntity = Entity::find($entity->id);
    $this->assertEquals($value1, $savedEntity->properties[$key1]);
    $this->assertEquals($value2, $savedEntity->properties[$key2]);
    $this->assertEquals($value3, $savedEntity->properties[$key3]);
  }

  /** @test */
  function an_id_can_be_set()
  {
    $entityId = 'my-id';
    $entity = Entity::factory()->create(['id' => $entityId]);
    $this->assertEquals($entityId, $entity->id);
  }

  /** @test */
  function an_id_is_generated()
  {
    $entity = Entity::factory()->create();
    $this->assertTrue(is_string($entity->id));
    $this->assertTrue(strlen($entity->id) === 10);
  }

  function assert_default_values($entity) {
    $this->assertEquals('Entity', $entity->model);
    $this->assertEquals('entity', $entity->view);
    $this->assertEquals(true, $entity->is_active);
    $this->assertEquals(1, $entity->version);
    $this->assertNotEquals(null, $entity->published_at);
  }
}
