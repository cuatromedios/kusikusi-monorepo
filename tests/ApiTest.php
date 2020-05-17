<?php

use Illuminate\Support\Carbon;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Illuminate\Database\Seeder;
use Illuminate\Eloquent\Model;
use Illuminate\Support\Str;
use Kusikusi\Models\EntityModel;

class ApiTest extends TestCase
{
    private $data = [
        'website' => ['id'=>'website', 'model'=>'website', 'view'=>'website'],
        'home' => ['id'=>'home', 'model'=>'home', 'view' =>'home', 'parent_entity_id'=>'home', 'properties'=>'"price":50.4'],
        'page_with_content' => ['id'=>'page', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home', 'contents'=>['title'=>['en_US'=>'The page', 'es_ES'=>'La pagina']]],
        'page_with_raw_content' => ['id'=>'pageraw', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home', 'contents'=>[["lang"=>"en_US", "field"=>"title", "text"=>"The raw page"], ["lang"=>"es_ES", "field"=>"title", "text"=>"La página raw"]]],
        'page_of_section_one' => ['id'=>'page_sone', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'section', 'contents'=>['title'=>['en_US'=>'The section one', 'es_ES'=>'La seccion uno']]],
        'page_of_section_two' => ['id'=>'page_stwo', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'section', 'contents'=>['title'=>['en_US'=>'The section two', 'es_ES'=>'La seccion dos']]],
        'page_of_section_ec_one' => ['id'=>'page_secone', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'section_ec', 'contents'=>['title'=>['en_US'=>'The section ec one', 'es_ES'=>'La seccion ec uno']]],
        'page_of_section_ec_two' => ['id'=>'page_sectwo', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'section_ec', 'contents'=>['title'=>['en_US'=>'The section ec two', 'es_ES'=>'La seccion ec dos']]],
        'section_entities_collection' => ['id'=>'section_ec','model'=>'section', 'view'=>'section', 'parent_entity_id'=>'home', 'contents'=>['title'=>['en_US'=>'Heading', 'es_ES'=>'El Titulo'], 'section'=>['en_US'=>'The page', 'es_ES'=>'La página'], 'slug'=>['en_US'=>'Route', 'es_ES'=>'Ruta']]],
        'section_with_content_slug' => ['id'=>'section', 'model'=>'section', 'view'=>'section', 'parent_entity_id'=>'home', 'contents'=>['title'=>['en_US'=>'The page', 'es_ES'=>'La pagina'], 'section'=>['en_US'=>'The page', 'es_ES'=>'La página'], 'slug'=>['en_US'=>'Hello', 'es_ES'=>'Hola']]],
        'medium' => ['id'=>'medium', 'parent_entity_id'=>'medium', 'model'=>'medium'],
        'select' => ['select'=>'id,model,properties,view,parent_entity_id,is_active,created_by,updated_by,published_at,unpublished_at,version,version_tree,version_relations,version_full,created_at,updated_at,deleted_at'],
        'select_records' => ['id'=>'home', 'model'=>'home', 'view'=>'home', 'parent_entity_id'=>'home', 'is_active'=>true, 'updated_by'=>null, 'created_by'=>null, 'unpublished_at'=>null, 'deleted_at'=>null, 'properties'=>'"price":50.4', 'version'=>1, 'version_tree'=>26, 'version_relations'=>0, 'version_full'=>27],
        'edit' => ['id'=>'page_sones', 'view'=>'pages', 'properties'=>'"prop":10.5', 'contents'=>['title'=>['en_US'=>'page', 'es_ES'=>'pagina']], 'relations'=>['called_entity_id'=>'section']],
        'create_relation' => ['called_entity_id'=>'medium', 'kind'=>'medium', 'tags'=>null, 'position' => 3, 'depth' => 4],
        'create_entity_with_relation' => ['model'=>'home', 'kind'=>'medium', 'tags'=>["1","2"], 'properties'=>'"price":50.4', 'position' => 3, 'depth' => 4]

    ];
    /**
     * A basic test example.
     *
     * @return void
     */
    /* public function setUp() :void {
        parent::setUp();
        $this->artisan('migrate:reset');
        $this->artisan('migrate');
        (new DatabaseSeeder())->call(ApiTestSeeder::class);
    } */

    public function testDatabaseSeeder()
    {
        $this->artisan('migrate:reset');
        $this->artisan('migrate');
        (new DatabaseSeeder())->call(ApiTestSeeder::class);
        $this->assertTrue(true);
    }

    public function testLoginWithCorrectData()
    {
        $json = [
            'email'=>'admin@example.com',
            'password'=>'Hello123'
        ];
        $user = $this->POST('/api/user/login', $json)
        ->seeStatusCode(200)->response->getContent();
        $auth = json_decode($user, true);
        $authorizationToken = $auth['token'];
        return $authorizationToken;
    }

    public function testLoginWithIncorrectData()
    {
        $json = [
            'email'=>'kusikusi',
            'password'=>'IncorrectPassword'
        ];
        $this->POST('/api/user/login', $json)
        ->seeStatusCode(401);

    }

    public function testCreateEntityWithInvalidToken()
    {
        $json = ['model'=>'website'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '])
        ->seeStatusCode(401);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOnlyModel($authorizationToken)
    {
        $json = ['model'=>'media'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['model'=>'media'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithoutModel($authorizationToken)
    {
        $json = ['id'=>'website'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(422);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOwnId($authorizationToken)
    {
        $json = $this->data['website'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['website'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithOptionalParameters($authorizationToken)
    {
        $json = $this->data['home'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['home'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        /* $review = $this->json('GET', '/api/entity/home', ['with'=>'entities_related'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['kind'=>'ancestor', 'depth'=>1])
        ->seeJsonContains(['kind'=>'ancestor', 'depth'=>2])
        ->seeStatusCode(200); */
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithContents($authorizationToken)
    {
        $json = $this->data['page_with_content'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entity/page', ['with'=>'contents'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['lang'=>'en_US', 'field'=>'title', 'text'=>'The page'])
        ->seeJsonContains(['lang'=>'es_ES', 'field'=>'title', 'text'=>'La pagina'])
        ->seeStatusCode(200);
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithRawContents($authorizationToken)
    {
        $json = $this->data['page_with_raw_content'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
            ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entity/pageraw', ['with'=>'contents'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
            ->seeJsonContains(['lang'=>'en_US', 'field'=>'title', 'text'=>'The raw page'])
            ->seeJsonContains(['lang'=>'es_ES', 'field'=>'title', 'text'=>'La página raw'])
            ->seeStatusCode(200);
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithContentSlug($authorizationToken)
    {
        $json = $this->data['section_with_content_slug'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $review = $this->json('GET', '/api/entities/section', ['with'=>'routes'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['path'=>'/Hello', 'lang'=>'es_ES'])
        ->seeJsonContains(['path'=>'/Hola', 'lang'=>'en_US'])
        ->seeStatusCode(200);
        return $entity_id;
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityWithRelationsDB($authorizationToken)
    {
        $json = $this->data['medium'];
        $response = $this->json('POST', '/api/entity', $json, ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['medium'])
        ->seeStatusCode(200);
        $auth = json_decode($response->response->getContent(), true);
        $entity_id = $auth['id'];
        $this->seeInDatabase('relations', ['caller_entity_id'=>'medium', 'kind'=>'ancestor', 'called_entity_id'=>'medium', 'depth'=>1]);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'medium', 'kind'=>'ancestor', 'called_entity_id'=>'medium', 'depth'=>2]);
        return $entity_id;
    }

    public function testSetCollectionEntities()
    {
        $website = new EntityModel($this->data['section_entities_collection']);
        $website->save();
        $website = new EntityModel($this->data['page_of_section_one']);
        $website->save();
        $website = new EntityModel($this->data['page_of_section_two']);
        $website->save();
        $website = new EntityModel($this->data['page_of_section_ec_one']);
        $website->save();
        $website = new EntityModel($this->data['page_of_section_ec_two']);
        $website->save();
        $this->assertTrue(true);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testOrderByEntitiesRecords($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['order-by'=>'model,view,contents.title'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>12])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testReadAllEntitiesRecords($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?&per-page=6&page=2', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>12, 'current_page'=>2])
        ->json('GET', '/api/entities?&per-page=6&page=1', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>12, 'current_page'=>1])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testSelectEntitiesRecords($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities/home', $this->data['select'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains($this->data['select_records'])
        ->seeStatusCode(200);
    }

     /**
     * @depends testLoginWithCorrectData
     */
    public function testSelectEntitiesContents($authorizationToken)
    {
        $response = $this->json('GET', '/api/entity/section', ['with'=>'contents'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['lang'=>'en_US', 'field'=>'title', 'text'=>'The page'])
        ->seeJsonContains(['lang'=>'en_US', 'field'=>'slug', 'text'=>'Hello'])
        ->seeJsonContains(['lang'=>'en_US', 'field'=>'section', 'text'=>'The page'])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testSelectEntitiesRoutes($authorizationToken)
    {
        $response = $this->json('GET', '/api/entity/section', ['with'=>'routes'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['path'=>'/Hola', 'lang'=>'es_ES'])
        ->seeJsonContains(['path'=>'/Hello', 'lang'=>'en_US'])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testSelectLangEntitiesRoutes($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?select=contents&lang=en_US', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testOfModelEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['of-model'=>'page'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>6])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 6);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testChildOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['child-of'=>'home'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>5])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 5);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testParentOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['parent-of'=>'page_sone'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>1])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 1);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testAncestorOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['ancestor-of'=>'page_sone'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>4])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 4);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testDescendantOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['descendant-of'=>'section'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>2])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 2);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testSiblingsOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities', ['siblings-of'=>'section'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>4])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 4);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testRelatingEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?relating=section:ancestor', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>2])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 2);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testRelatedByEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?related-by=section:ancestor', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>3])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 3);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testOfModelPlusChildOfEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?of-model=page', ['child-of'=>'home'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>2])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 2);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testAncestorOfPlusOfModelEntitiesCollection($authorizationToken)
    {
        $response = $this->json('GET', '/api/entities?ancestor-of=page_sone', ['of-model'=>'home'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['total'=>3])
        ->seeStatusCode(200);
        $json = json_decode($response->response->getContent(), true);
        $data = count($json['data']);
        $this->assertEquals($data, 3);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testEditEntity($authorizationToken)
    {
        $response = $this->json('PATCH', '/api/entity/page_sone', $this->data['edit'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['id'=>'page_sones', 'view'=>'pages', 'properties'=>'"prop":10.5'])
        ->seeStatusCode(200);
        $response = $this->json('GET', '/api/entity/page_sones', ['with'=>'contents'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['lang'=>'en_US', 'field'=>'title', 'text'=>'page'])
        ->seeJsonContains(['lang'=>'es_ES', 'field'=>'title', 'text'=>'pagina'])
        ->seeStatusCode(200);
    }

     /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateEntityRelation($authorizationToken)
    {
        $response = $this->json('POST', '/api/entity/pageraw/relation', $this->data['create_relation'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['caller_entity_id'=>'pageraw', 'called_entity_id'=>'medium', 'kind'=>'medium'])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testDeleteEntityRelation($authorizationToken)
    {
        $response = $this->json('DELETE', '/api/entity/pageraw/relation/medium/medium', [], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testGetCMSConfiguration($authorizationToken)
    {
        $response = $this->json('GET', '/api/cms/config', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testReturnsLoggedUser($authorizationToken)
    {
        $response = $this->json('GET', '/api/user/me', [''=>''], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        ->seeJsonContains(['email'=>'admin@example.com', 'name'=>'Administrator', 'profile'=>'admin'])
        ->seeStatusCode(200);
    }

    /**
     * @depends testLoginWithCorrectData
     */
    public function testCreateAndRelateEndpoint($authorizationToken)
    {
        $response = $this->json('POST', '/api/entity/pageraw/create_and_relate', $this->data['create_entity_with_relation'], ['HTTP_Authorization' => 'Bearer '.$authorizationToken])
        /* ->seeJsonContains([
            "entities_relating" => [
                [ "id" => "pageraw" ]
            ]
        ]) */
        ->seeStatusCode(200);
    }
}
