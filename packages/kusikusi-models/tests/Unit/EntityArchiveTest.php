<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityArchive;
use Kusikusi\Models\EntityContent;
use Kusikusi\Models\EntityRelation;


class EntityArchiveTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function an_archive_as_version_can_be_created()
  {
    $id = 'EntityOne';                $model = 'EntityModel';  
    $view = 'EntityView';             $properties = '{"title": "Title"}';  
    $parentEntity = 'EntityParent';   $kind = 'version';
    Entity::create(['id' => $parentEntity]);
    Entity::create(['id' => $id, 'model' => $model, 'view' => $view, 'properties' => $properties, 'parent_entity_id' => $parentEntity]);
    EntityArchive::archive($id, $kind);
    $archive = EntityArchive::select()->where('entity_id', $id)->first();      
    $archivePayload = EntityArchive::select('payload')->where('entity_id', $id )->value('payload');  
    $jsonArchive = json_decode($archivePayload);
    $jsonProperties = json_decode($properties);
    $this->assertEquals($archive->entity_id, $id);
    $this->assertEquals($archive->kind, $kind);
    $this->assertEquals($jsonArchive->id, $id);
    $this->assertEquals($jsonArchive->model, $model);
    $this->assertEquals($jsonArchive->view, $view);
    $this->assertEquals($jsonArchive->properties, $jsonProperties);
    $this->assertEquals($jsonArchive->parent_entity_id, $parentEntity);
  }

  /** @test */
  function an_archive_as_draft_can_be_created()
  {
    $id = 'EntityOne';                $model = 'EntityModel';  
    $view = 'EntityView';             $properties = '{"title": "Title"}';  
    $parentEntity = 'EntityParent';   $kind = 'draft';
    Entity::create(['id' => $parentEntity]);
    Entity::create(['id' => $id, 'model' => $model, 'view' => $view, 'properties' => $properties, 'parent_entity_id' => $parentEntity]);
    EntityArchive::archive($id, $kind);
    $archive = EntityArchive::select()->where('entity_id', $id)->where('kind', $kind)->first();      
    $archivePayload = EntityArchive::select('payload')->where('entity_id', $id )->value('payload');  
    $jsonArchive = json_decode($archivePayload);
    $jsonProperties = json_decode($properties);
    $this->assertEquals($archive->entity_id, $id);
    $this->assertEquals($archive->kind, $kind);
    $this->assertEquals($jsonArchive->id, $id);
    $this->assertEquals($jsonArchive->model, $model);
    $this->assertEquals($jsonArchive->view, $view);
    $this->assertEquals($jsonArchive->properties, $jsonProperties);
    $this->assertEquals($jsonArchive->parent_entity_id, $parentEntity);
  }
  
  /** @test */
  function an_archive_with_contents_can_be_created()
  {
    $config = Config::set('kusikusi_models.store_versions', false);
    $id = 'EntityId';       $lang = 'en';
    $title = 'Title';       $description = 'Description';
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'title', 'text' => $title]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'description', 'text' => $description]);
    EntityArchive::archive($id, 'version');
    $archive = EntityArchive::where('entity_id', $id)->get();               
    $archivePayload = EntityArchive::select('payload')->where('entity_id', $id )->value('payload');  
    $jsonArchive = json_decode($archivePayload);
    $this->assertEquals($jsonArchive->id, $id);
    $this->assertEquals($jsonArchive->contents[0]->text, $title);
    $this->assertEquals($jsonArchive->contents[0]->lang, $lang);
    $this->assertEquals($jsonArchive->contents[1]->text, $description);
    $this->assertEquals($jsonArchive->contents[1]->lang, $lang);
  }
  
  /** @test */
  function an_archive_with_relations_can_be_created()
  {
    $config = Config::set('kusikusi_models.store_versions', false);
    $id1 = 'EntityOne';     $id2 = 'EntityTwo';
    $id3 = 'EntityThree';   $medium = EntityRelation::RELATION_MEDIA;
    $menu = EntityRelation::RELATION_MENU;
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2]);
    Entity::create(['id' => $id3]);
    EntityRelation::create(["caller_entity_id" => $id1, "called_entity_id" => $id2, "kind" => $medium, "depth" => 1]);
    EntityRelation::create(["caller_entity_id" => $id1, "called_entity_id" => $id3, "kind" => $menu, "depth" => 2]);    
    EntityArchive::archive($id1, 'version');
    $archive = EntityArchive::where('entity_id', $id1)->get();               
    $archivePayload = EntityArchive::select('payload')->where('entity_id', $id1 )->value('payload');  
    $jsonArchive = json_decode($archivePayload);
    $this->assertEquals($jsonArchive->entities_related[0]->id, $id2);
    $this->assertEquals($jsonArchive->entities_related[0]->relation->kind, $medium);
    $this->assertEquals($jsonArchive->entities_related[0]->relation->depth, 1);
    $this->assertEquals($jsonArchive->entities_related[1]->id, $id3);
    $this->assertEquals($jsonArchive->entities_related[1]->relation->kind, $menu);
    $this->assertEquals($jsonArchive->entities_related[1]->relation->depth, 2);
  }
  
  /** @test */
  function an_update_from_archive_can_be_done()
  {
    $config = Config::set('kusikusi_models.store_versions', false);
    $id1 = 'EntityOne';     $model1 = 'ModelOne';       $view1 = 'ViewOne';
    $id2 = 'EntityTwo';     $model2 = 'ModelTwo';       $view2 = 'ViewTwo';
    Entity::create(['id' => $id1, 'model' => $model1, 'view' => $view1]);
    Entity::create(['id' => $id2, 'model' => $model2, 'view' => $view2]);
    EntityArchive::archive($id1, 'version');
    EntityArchive::archive($id2, 'version');
    EntityArchive::updateFromArchive($id1, 2);
    $entityUpdated = Entity::select()->find($id1);               
    $this->assertEquals($entityUpdated->model, $model2);
    $this->assertEquals($entityUpdated->view, $view2);
  }
  
  /** @test */
  function an_archive_can_be_duplicated()
  {
    $config = Config::set('kusikusi_models.store_versions', false);
    $id1 = 'EntityOne';       $id2 = 'EntityTwo';     
    $model2 = 'ModelTwo';     $view2 = 'ViewTwo';
    Entity::create(['id' => $id1]);
    Entity::create(['id' => $id2, 'model' => $model2, 'view' => $view2]);
    EntityArchive::archive($id2, 'version');
    EntityArchive::updateFromArchive($id1, 1);
    $entityDuplicated = Entity::with('contents')->find($id1);  
    $out = new \Symfony\Component\Console\Output\ConsoleOutput();    
    $this->assertEquals($entityDuplicated->model, $model2);
    $this->assertEquals($entityDuplicated->view, $view2);
  }

}
