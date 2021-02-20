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
  function an_id_can_be_set()
  {
    $entityId = 'my-id';
    $entity = Entity::factory()->create(['id' => $entityId]);
    $this->assertEquals($entityId, $entity->id);
  }

  function assert_default_values($entity) {
    $this->assertEquals('Entity', $entity->model);
    $this->assertEquals('entity', $entity->view);
    $this->assertEquals(true, $entity->is_active);
    $this->assertEquals(1, $entity->version);
    $this->assertNotEquals(null, $entity->published_at);
  }
}
