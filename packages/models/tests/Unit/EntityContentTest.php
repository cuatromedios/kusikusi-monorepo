<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;

class EntityContentTest extends TestCase
{
  // use RefreshDatabase;

  /** @test */
  function a_entity_content_can_be_created()
  {
    EntityContent::create(['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'title', 'text' => 'Title']);
    $this->assertDatabaseCount((new EntityContent())->getTable(), 1);
  }

  /** @test */
  function a_entity_content_cannot_be_created_with_required_fields()
  {
    $this->expectException(\Illuminate\Database\QueryException::class);
    EntityContent::create(['entity_id' => 'some-id']);
  }

  /** @test */
  function a_entity_content_can_be_created_and_retrived()
  {
    $entityData = ['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'title', 'text' => 'Title'];
    $entityData2 = ['entity_id' => 'some-other-id', 'lang' => 'en', 'field' => 'title', 'text' => 'Other Title'];
    EntityContent::create($entityData);
    EntityContent::create($entityData2);
    $this->assertDatabaseCount((new EntityContent())->getTable(), 2);
    $this->assertDatabaseHas((new EntityContent())->getTable(), $entityData);
  }

  /** @test */
  function several_entity_contents_can_be_created()
  {
    $content1 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'title', 'text' => 'Title']);
    $content2 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'slug', 'text' => 'title']);
    $content3 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'es', 'field' => 'title', 'text' => 'Título']);
    $content3 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'es', 'field' => 'slug', 'text' => 'titulo']);
    $this->assertDatabaseCount((new EntityContent())->getTable(), 4);
  }

  /** @test */
  function entity_contents_with_same_index_does_not_duplicate()
  {
    $content1 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'title', 'text' => 'Title']);
    $content3 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'es', 'field' => 'title', 'text' => 'Título']);
    $content2 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'en', 'field' => 'title', 'text' => 'New Title']);
    $content3 = EntityContent::create(['entity_id' => 'some-id', 'lang' => 'es', 'field' => 'title', 'text' => 'Nuevo Título']);
    $this->assertDatabaseCount((new EntityContent())->getTable(), 2);
  }

}
