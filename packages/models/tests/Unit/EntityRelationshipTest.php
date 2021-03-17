<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityRelation;

class EntityRelationshipTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function a_entity_relationship_can_be_created()
  {
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $relationData = EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => 'principal']);
    $this->assertDatabaseCount((new EntityRelation())->getTable(), 1);
  }

  /** @test */
  function a_entity_relationship_cannot_be_created_with_required_fields()
  {
    $this->expectException(\Illuminate\Database\QueryException::class);
    EntityRelation::create(['kind' => 'some-id']);
  }

  /** @test */
  function a_entity_relationship_can_be_created_and_retrived()
  {
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $entityData3 = Entity::factory()->create();
    $relationData = EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $relationData2 = EntityRelation::create(['caller_entity_id' => $entityData2->id, 'called_entity_id' => $entityData3->id, 'kind' => 'Relationship2', 'position' => 2, 'depth' => 2, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $this->assertDatabaseHas((new EntityRelation())->getTable(), [ 'caller_entity_id' => $entityData->id]);
    $this->assertDatabaseHas((new EntityRelation())->getTable(), [ 'caller_entity_id' => $entityData2->id]);
  }

  /** @test */
  function several_entity_relationship_can_be_created()
  {
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $entityData3 = Entity::factory()->create();
    EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    EntityRelation::create(['caller_entity_id' => $entityData2->id, 'called_entity_id' => $entityData3->id, 'kind' => 'Relationship2', 'position' => 1, 'depth' => 2, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    EntityRelation::create(['caller_entity_id' => $entityData3->id, 'called_entity_id' => $entityData->id, 'kind' => 'Relationship3', 'position' => 1, 'depth' => 3, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData3->id, 'kind' => 'Relationship4', 'position' => 1, 'depth' => 4, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $this->assertDatabaseCount((new EntityRelation())->getTable(), 4);
  }

  /** @test */
  function entity_relationships_with_same_index_does_not_duplicate()
  {
    $this->expectException(\Illuminate\Database\QueryException::class);
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $entityData3 = Entity::factory()->create();
    EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $repeat1 = EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $this->assertDatabaseCount((new EntityRelation())->getTable(), 2);
  }

  /** @test */
  function entity_relationships_are_updated()
  {
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $relation = EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $relation->kind = 'Relation';
    $relation->position = 2;
    $relation->depth = 2;
    $relation->updated_at = '2021-03-16 12:00:00';
    $relation->update();
    $this->assertDatabaseCount((new EntityRelation())->getTable(), 1);
  }

  /** @test */
  function entity_relationships_are_cleared()
  {
    $entityData = Entity::factory()->create();
    $entityData2 = Entity::factory()->create();
    $relation = EntityRelation::create(['caller_entity_id' => $entityData->id, 'called_entity_id' => $entityData2->id, 'kind' => 'Relationship', 'position' => 1, 'depth' => 1, 'tags' => null, 'created_at' => '2021-03-10 10:00:00', 'updated_at' => null]);
    $relation->delete();
    $this->assertDatabaseCount((new EntityRelation())->getTable(), 1);
  }
}
