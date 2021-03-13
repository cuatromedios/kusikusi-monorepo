<?php

namespace Kusikusi\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Kusikusi\Tests\TestCase;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;
use Kusikusi\Models\EntityRoute;


class EntityRouteTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  function a_route_can_be_created()
  {
    $id = 'EntityOne';      $lang = 'en';
    $slug = 'title';
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);        
    $routes = EntityRoute::select()->where('entity_id', $id)->first();    
    $this->assertEquals($routes->path, "/$slug");
    $this->assertEquals($routes->lang, $lang);
    $this->assertEquals($routes->kind, 'main');
  }
  
  /** @test */
  function a_route_can_be_multilanguage()
  {
    $id = 'EntityOne';      
    $lang1 = 'en';         $lang2 = 'es';
    $slug1 = 'title';      $slug2 = 'título'; 
    $kind = 'main'; 
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang1, 'field' => 'slug', 'text' => $slug1]);        
    EntityContent::create(['entity_id' => $id, 'lang' => $lang2, 'field' => 'slug', 'text' => $slug2]);        
    $routes = EntityRoute::select()->where('entity_id', $id)->get();    
    $this->assertEquals($routes[0]->path, "/$slug1");
    $this->assertEquals($routes[1]->path, "/$slug2");
    $this->assertEquals($routes[0]->lang, $lang1);
    $this->assertEquals($routes[1]->lang, $lang2);
    $this->assertEquals($routes[0]->kind, $kind);
    $this->assertEquals($routes[1]->kind, $kind);
  }

  /** @test */
  function a_redirect_route_can_be_created()
  {
    $id = 'EntityOne';      
    $lang = 'en';       $slug = 'title';       
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);        
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);        
    $routes = EntityRoute::select()->where('entity_id', $id)->get();   
    $this->assertEquals($routes[0]->path, "/$slug");
    $this->assertEquals($routes[1]->path, "/$slug");
    $this->assertEquals($routes[0]->lang, $lang);
    $this->assertEquals($routes[1]->lang, $lang);
    $this->assertEquals($routes[0]->kind, 'permanent_redirect');
    $this->assertEquals($routes[1]->kind, 'main');
  }
  
  /** @test */
  function a_route_without_config_cant_be_created()
  {
    $config = Config::set('kusikusi_models.create_routes_from_slugs', false);
    $id = 'EntityOne';      $lang = 'en';
    $slug = 'title';
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);        
    $routes = EntityRoute::select()->where('entity_id', $id)->first();    
    $this->assertEmpty($routes);
  }
  
  /** @test */
  function a_permanent_route_without_config_cant_be_created()
  {
    $config = Config::set('kusikusi_models.create_routes_redirects', false);
    $id = 'EntityOne';      $lang = 'en';
    $slug = 'title';
    Entity::create(['id' => $id]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);
    EntityContent::create(['entity_id' => $id, 'lang' => $lang, 'field' => 'slug', 'text' => $slug]);         
    $routes = EntityRoute::select()->where('entity_id', $id)->get();  
    $this->assertEquals(count($routes), 1);
  }
}
