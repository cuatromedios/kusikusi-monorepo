<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityRelation;
use Kusikusi\Models\EntityContent;
use Illuminate\Support\Carbon;


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
    $this->assertEquals('public', $entity->visibility);
    $this->assertEquals(1, $entity->version);
    $this->assertNotEquals(null, $entity->published_at);
  }

  /** @test */
  function scope_with_content_can_be_used()
  {
    $id1 = 'EntityOne';     $id2 = 'EntityTwo';
    $title = 'Title';       $description = 'Description';
    $titulo = 'Título';     $descripcion = 'Descripción';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2]);
    EntityContent::create(['entity_id' => $id1, 'lang' => 'en', 'field' => 'title', 'text' => $title]);
    EntityContent::create(['entity_id' => $id1, 'lang' => 'en', 'field' => 'description', 'text' => $description]);
    EntityContent::create(['entity_id' => $id2, 'lang' => 'es', 'field' => 'title', 'text' => $titulo]);
    EntityContent::create(['entity_id' => $id2, 'lang' => 'es', 'field' => 'description', 'text' => $descripcion]);
    $entityOneWithContent = Entity::select()->withContent('en')->where('id', $id1)->first();
    $entityTwoWithContent = Entity::select()->withContent('es')->where('id', $id2)->first();
    $this->assertEquals($entityOneWithContent->content['title'], $title);
    $this->assertEquals($entityOneWithContent->content['description'], $description);
    $this->assertEquals($entityTwoWithContent->content['title'], $titulo);
    $this->assertEquals($entityTwoWithContent->content['description'], $descripcion);
    $entityOneWithSpecificContent = Entity::select()->withContent('en', 'title')->where('id', $id1)->first();
    $this->assertEquals($entityOneWithSpecificContent->content['title'], $title);
    $this->assertEquals($entityOneWithSpecificContent->content['description'] ?? null, null);
    $entityOneWithSpecificContent = Entity::select()->withContent('en', ['description'])->where('id', $id1)->first();
    $this->assertEquals($entityOneWithSpecificContent->content['description'], $description);
    $this->assertEquals($entityOneWithSpecificContent->content['title'] ?? null, null);
}

//TODO: Needs to include Medium model
/** @no_test */
function scope_with_medium_can_be_used()
{
  $e1 = 'entity_1';
  $m1 = 'medium_1';
  $m2 = 'medium_2';
  $m3 = 'medium_3';
  $medium_title = 'The title medium';
  Entity::create(['id' => $e1]);
  Entity::create(['id' => $m1]);
  Entity::create(['id' => $m2]);
  Entity::create(['id' => $m3]);
  EntityRelation::create([ 'kind' => 'medium', 'caller_entity_id' => $e1,  'called_entity_id' => $m1,  'tags' => ['slider'] ]);
  EntityRelation::create([ 'kind' => 'medium', 'caller_entity_id' => $e1,  'called_entity_id' => $m2,  'tags' => ['icon'] ]);
  EntityRelation::create([ 'kind' => 'medium', 'caller_entity_id' => $e1,  'called_entity_id' => $m3,  'tags' => ['slider'] ]);
  $entity = Entity::select()->withMedium('icon')->where('id', $e1)->first();
  $this->assertEquals($entity->medium->id, $m1);
}

  /** @test */
  function scope_of_model_can_be_used()
  {
    $model = 'EntityTest';
    Entity::create(['model' => $model]);
    Entity::create(['model' => $model]);
    Entity::create(['model' => 'EntityTwoTest']);
    $entityOfModel = Entity::select()->ofModel($model)->get();
    foreach( $entityOfModel as $entity ) {
      $this->assertEquals($model, $entity->model);
    }
  }
  
  /** @test */
  function scope_is_published_can_be_used()
  {
    $now = Carbon::now();
    Entity::create(['visibility' => 'public']);
    Entity::create(['visibility' => 'private']);
    Entity::create(['published_at' => $now]);
    Entity::create(['unpublished_at' => $now]);
    $PublishedEntities = Entity::select()->isPublished()->get();
    $this->assertEquals(count($PublishedEntities), 2);
  }

  /** @test */
  function scope_children_of_can_be_used()
  {
    $id = 'EntityTest';
    Entity::create(['id' => $id]);
    Entity::create(['parent_entity_id' => $id]);
    Entity::create(['parent_entity_id' => $id]);
    $childrenOfEntity = Entity::select()->childrenOf($id)->get();
    $relationEntity = EntityRelation::where('called_entity_id', $id)->get();
    $this->assertEquals($childrenOfEntity[0]->id, $relationEntity[0]->caller_entity_id);
    $this->assertEquals($childrenOfEntity[1]->id, $relationEntity[1]->caller_entity_id);
    $this->assertEquals($relationEntity[0]->kind, 'ancestor');
    $this->assertEquals($relationEntity[1]->kind, 'ancestor');
  }

  /** @test */
  function scope_parent_of_can_be_used()
  {
    $id = 'EntityParent';
    $id2 = 'EntitySon';
    Entity::create(['id' => $id]);
    Entity::create(['id' => $id2, 'parent_entity_id' => $id]);
    $parentOfEntity = Entity::select()->parentOf($id2)->first();
    $relationEntity = EntityRelation::where('caller_entity_id', $id2)->first();
    $this->assertEquals($parentOfEntity->id, $relationEntity->called_entity_id);
    $this->assertEquals($relationEntity->kind, 'ancestor');
    $this->assertEquals($relationEntity->depth, 1);
  }
  
  /** @test */
  function scope_ancestors_of_can_be_used()
  {
    $id1 = 'EntityGrandParent';   $id2 = 'EntityParent';
    $id3 = 'EntitySon';           $id4 = 'EntityGrandGrandParent';
    $kind = 'ancestor';
    Entity::create(['id' => $id4]);
    Entity::create(['id' => $id1, 'parent_entity_id' => $id4]);
    Entity::create(['id' => $id2, 'parent_entity_id' => $id1]);
    Entity::create(['id' => $id3, 'parent_entity_id' => $id2]);
    $ancestorsOfEntity = Entity::select()->ancestorsOf($id3)->get();
    $this->assertEquals($ancestorsOfEntity[0]->id, $id2);
    $this->assertEquals($ancestorsOfEntity[0]->depth, 1);
    $this->assertEquals($ancestorsOfEntity[0]->kind, $kind);
    $this->assertEquals($ancestorsOfEntity[1]->id, $id1);
    $this->assertEquals($ancestorsOfEntity[1]->depth, 2);
    $this->assertEquals($ancestorsOfEntity[1]->kind, $kind);
    $this->assertEquals($ancestorsOfEntity[2]->id, $id4);
    $this->assertEquals($ancestorsOfEntity[2]->depth, 3);
    $this->assertEquals($ancestorsOfEntity[2]->kind, $kind);
  }

  /** @test */
  function scope_descendants_of_can_be_used()
  {
    $id1 = 'EntityGrandParent';   $id2 = 'EntityParent';
    $id3 = 'EntitySon';           $id4 = 'EntityGrandSon1';
    $id5 = 'EntityGrandSon2';
    $kind = 'ancestor';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2, 'parent_entity_id' => $id1]);
    Entity::create(['id' => $id3, 'parent_entity_id' => $id2]);
    Entity::create(['id' => $id4, 'parent_entity_id' => $id3]);
    Entity::create(['id' => $id5, 'parent_entity_id' => $id3]);
    $descendantofEntity = Entity::select()->descendantsOf($id1)->get();
    $this->assertEquals($descendantofEntity[0]->id, $id2);
    $this->assertEquals($descendantofEntity[0]->depth, 1);
    $this->assertEquals($descendantofEntity[0]->kind, $kind);
    $this->assertEquals($descendantofEntity[1]->id, $id3);
    $this->assertEquals($descendantofEntity[1]->depth, 2);
    $this->assertEquals($descendantofEntity[1]->kind, $kind);
    $this->assertEquals($descendantofEntity[2]->id, $id4);
    $this->assertEquals($descendantofEntity[2]->depth, 3);
    $this->assertEquals($descendantofEntity[2]->kind, $kind);
    $this->assertEquals($descendantofEntity[3]->id, $id5);
    $this->assertEquals($descendantofEntity[3]->depth, 3);
    $this->assertEquals($descendantofEntity[3]->kind, $kind);
  }
  
  /** @test */
  function scope_siblings_of_can_be_used()
  {
    $id1 = 'EntityParent';  $id2 = 'EntitySon';
    $id3 = 'EntitySon2';    $id4 = 'EntitySon3';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2, 'parent_entity_id' => $id1]);
    Entity::create(['id' => $id3, 'parent_entity_id' => $id1]);
    Entity::create(['id' => $id4, 'parent_entity_id' => $id1]);
    $siblingsofEntity = Entity::select()->siblingsOf($id2)->get();
    $this->assertEquals($siblingsofEntity[0]->id, $id3);
    $this->assertEquals($siblingsofEntity[0]->depth, 1);
    $this->assertEquals($siblingsofEntity[0]->kind, 'ancestor');
    $this->assertEquals($siblingsofEntity[1]->id, $id4);
    $this->assertEquals($siblingsofEntity[1]->depth, 1);
    $this->assertEquals($siblingsofEntity[1]->kind, 'ancestor');
  }
  
  /** @test */
  function scope_related_by_can_be_used()
  {
    $id1 = 'EntityCalled';  $id2 = 'EntityOne';
    $id3 = 'EntityTwo';     $id4 = 'EntityThree';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2]);
    Entity::create(['id' => $id3]);
    Entity::create(['id' => $id4]);
    EntityRelation::create(["caller_entity_id" => $id1, "called_entity_id" => $id2, "kind" => EntityRelation::RELATION_MEDIA, "depth" => 1]);
    EntityRelation::create(["caller_entity_id" => $id1, "called_entity_id" => $id3, "kind" => EntityRelation::RELATION_MENU, "depth" => 2]);
    EntityRelation::create(["caller_entity_id" => $id1, "called_entity_id" => $id4, "kind" => EntityRelation::RELATION_UNDEFINED, "depth" => 3]);
    $relatedByEntity = Entity::select()->relatedBy($id1)->get();
    $this->assertEquals($relatedByEntity[0]->id, $id2);
    $this->assertEquals($relatedByEntity[0]->depth, 1);
    $this->assertEquals($relatedByEntity[0]->kind, 'medium');
    $this->assertEquals($relatedByEntity[1]->id, $id3);
    $this->assertEquals($relatedByEntity[1]->depth, 2);
    $this->assertEquals($relatedByEntity[1]->kind, 'menu');
    $this->assertEquals($relatedByEntity[2]->id, $id4);
    $this->assertEquals($relatedByEntity[2]->depth, 3);
    $this->assertEquals($relatedByEntity[2]->kind, 'relation');
  }
  
  /** @test */
  function scope_relating_can_be_used()
  {
    $id1 = 'EntityCaller';  $id2 = 'EntityOne';
    $id3 = 'EntityTwo';     $id4 = 'EntityThree';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2]);
    Entity::create(['id' => $id3]);
    Entity::create(['id' => $id4]);
    EntityRelation::create(["caller_entity_id" => $id2, "called_entity_id" => $id1, "kind" => EntityRelation::RELATION_MEDIA, "depth" => 1]);
    EntityRelation::create(["caller_entity_id" => $id3, "called_entity_id" => $id1, "kind" => EntityRelation::RELATION_MENU, "depth" => 2]);
    EntityRelation::create(["caller_entity_id" => $id4, "called_entity_id" => $id1, "kind" => EntityRelation::RELATION_UNDEFINED, "depth" => 3]);
    $relatingEntity = Entity::select()->relating($id1)->get();
    $this->assertEquals($relatingEntity[0]->id, $id2);
    $this->assertEquals($relatingEntity[0]->depth, 1);
    $this->assertEquals($relatingEntity[0]->kind, 'medium');
    $this->assertEquals($relatingEntity[1]->id, $id3);
    $this->assertEquals($relatingEntity[1]->depth, 2);
    $this->assertEquals($relatingEntity[1]->kind, 'menu');
    $this->assertEquals($relatingEntity[2]->id, $id4);
    $this->assertEquals($relatingEntity[2]->depth, 3);
    $this->assertEquals($relatingEntity[2]->kind, 'relation');
  }
}
