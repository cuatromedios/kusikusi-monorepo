<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Kusikusi\Models\EntityModel;
use Kusikusi\Models\EntityContent;


class EntityModelTest extends TestCase
{
    use DatabaseMigrations;

    private $data = [
        'website' => ['id'=>'website', 'model'=>'website', 'view' =>'website'],
        'home' =>['id'=>'home', 'model'=>'home', 'view'=>'home', 'parent_entity_id'=>'website'],
        'website_without_model'=>['id'=>'website', 'view'=>'website'],
        'page' => ['id'=>'page', 'model'=>'page', 'view'=>'page', 'parent_entity_id'=>'home'],
        'page_with_content' => [
            'id'=>'page',
            'model'=>'page',
            'view'=>'page',
            'parent_entity_id'=>'home',
            'contents'=>[
                'title'=>['en'=>"The page", 'es'=>'La página'],
                'slug'=>['en'=>"the-page", 'es'=>'la-pagina']
            ]
        ],
        'section_with_content' => [
            'id'=>'section',
            'model'=>'section',
            'view'=>'section',
            'parent_entity_id'=>'home',
            'contents'=>[
                'title'=>['en'=>"The section", 'es'=>'La sección'],
                'slug'=>['en'=>"the-section", 'es'=>'la-seccion']
            ]
        ],
        'page_with_raw_content' => [
            'id'=>'pageraw',
            'model'=>'page',
            'view'=>'page',
            'parent_entity_id'=>'home',
            'contents'=>[
                [ "field"=>"title", "lang"=>"en", "text"=>"The page raw"],
                [ "field"=>"title", "lang"=>"es", "text"=>"La página raw"],
                [ "field"=>"slug", "lang"=>"en", "text"=>"the-page-raw"],
                [ "field"=>"slug", "lang"=>"es", "text"=>"la-pagina-raw"]
            ]
        ]
    ];

    /* *
     * A basic test example.
     *
     * @return void
     */
    public function testCreateEntity()
    {
        $website = new EntityModel($this->data['website']);
        $website->save();
        $this->seeInDatabase('entities',$this->data['website']);
    }

    public function testEditEntity()
    {
        $website = new EntityModel($this->data['website']);
        $website->save();
        $website = EntityModel::where('id',"website")->update($this->data['home']);
        $this->seeInDatabase('entities',$this->data['home']);
    }

     public function testDeleteEntity()
    {
        $website = new EntityModel($this->data['website']);
        $website->save();
        $delete = EntityModel::where('id', 'home')->delete();
        $this->notSeeInDatabase('entities',['id' => 'home']);
    }

    public function testCreateEntityWithoutModel()
    {
        $this->expectExceptionMessage('A model name is requiered to create a new entity');
        $website = new EntityModel($this->data['website_without_model']);
        $website->save();
    }

    public function testAncestorsParentEntityId()
    {
        $website = new EntityModel($this->data['website']);
        $home = new EntityModel($this->data['home']);
        $page = new EntityModel($this->data['page']);
        $website->save();
        $home->save();
        $page->save();
        $this->seeInDatabase('entities', $this->data['page']);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'page', 'kind'=>'ancestor', 'called_entity_id'=>'home', 'depth'=>1]);
        $this->seeInDatabase('relations', ['caller_entity_id'=>'page', 'kind'=>'ancestor', 'called_entity_id'=>'website', 'depth'=>2]);
        $ancestors = EntityModel::select('id')->ancestorOf('page')->orderBy('ancestor_relation_depth')->get()->toArray();
        $this->assertEquals(count($ancestors), 2);
        $this->assertEquals($ancestors[0]['id'], 'home');
        $this->assertEquals($ancestors[1]['id'], 'website');
    }

    public function testCreateEntityContent()
    {
        $data = [$this->data['page_with_content'], $this->data['section_with_content']];
        $totalCount = 0;
        foreach ($data as $entity_data) {
            $rowCount = 0;
            $entity = new EntityModel($entity_data);
            $entity->save();
            $this->seeInDatabase('entities', ['id'=>$entity_data['id']]);
            foreach ($entity_data['contents'] as $field=>$values) {
                foreach ($values as $lang=>$text) {
                    $this->seeInDatabase('contents', [
                        'entity_id'=>$entity_data['id'],
                        'field'=>$field,
                        'lang'=>$lang,
                        'text'=>$text
                    ]);
                    $rowCount++;
                    $totalCount++;
                }
            }
            $this->assertEquals($rowCount, EntityContent::where('entity_id', $entity_data['id'])->get()->count());
        }
        $this->assertEquals($totalCount, EntityContent::get()->count());
    }

    public function testCreateEntityWithRawContent()
    {
        $data = [$this->data['page_with_raw_content']];
        $totalCount = 0;
        foreach ($data as $entity_data) {
            $rowCount = 0;
            $entity = new EntityModel($entity_data);
            $entity->save();
            $this->seeInDatabase('entities', ['id'=>$entity_data['id']]);
            foreach ($entity_data['contents'] as $rawContent) {
                $this->seeInDatabase('contents', [
                    'entity_id'=>$entity_data['id'],
                    'field'=>$rawContent['field'],
                    'lang'=>$rawContent['lang'],
                    'text'=>$rawContent['text']
                ]);
                $rowCount++;
                $totalCount++;
            }
            $this->assertEquals($rowCount, EntityContent::where('entity_id', $entity_data['id'])->get()->count());
        }
        $this->assertEquals($totalCount, EntityContent::get()->count());
    }

  /*   public function testEntityContentRoutes()
    {
        factory(EntityModel::class)->create($this->data['page']);
        $this->seeInDatabase('entities',['id'=>'website']);
        $this->seeInDatabase('contents',$this->data['content_data']);

        $this->assertTrue($modelOne->is($modelTwo));

    } */
}
