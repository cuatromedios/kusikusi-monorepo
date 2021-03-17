<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Routing\Controller as BaseController;
use Kusikusi\Models\Entity;

class EntityController extends BaseController
{

    const ID_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]+$/';
    const ID_RULE_WITH_FILTER = 'string|min:1|max:64|regex:/^[A-Za-z0-9_-]+:?[a-z0-9]*$/';
    const MODEL_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9-]+$/';
    const TIMEZONED_DATE = 'nullable|date_format:Y-m-d\TH:i:sP|after_or_equal:1000-01-01T00:00:00-12:00|before_or_equal:9999-12-31T23:59:59-12:00';
    private $calledRelations = [];
    private $addedSelects = [];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get a collection of  entities.
     *
     * Returns a paginated collection of entities, filtered by all set conditions.
     *
     * @group Entity
     * @authenticated
     * @queryParam select A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax. Example: id,model,properties.price
     * @queryParam of-model (filter) The name of the model the entities should be, this will also call the query using the given model and not the default Entity model. Example: medium, page
     * @queryParam only-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam children-of (filter) The id or short id of the entity the result entities should be child of. Example: home
     * 
     * @queryParam order-by A comma separated lis of fields to order by. Example: model,properties.price:desc,contents.title
     * @queryParam where A comma separated list of conditions to met, Example: created_at>2020-01-01,properties.isImage,properties.format:png,model:medium
     * @queryParam parent-of (filter) The id or short id of the entity the result entities should be parent of (will return only one). Example: 8fguTpt5SB
     * @queryParam ancestor-of (filter) The id or short id of the entity the result entities should be ancestor of. Example: enKSUfUcZN
     * @queryParam descendant-of (filter) The id or short id of the entity the result entities should be descendant of. Example: xAaqz2RPyf
     * @queryParam siblings-of (filter) The id or short id of the entity the result entities should be siblings of. Example: _tuKwVy8Aa
     * @queryParam related-by (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: ElFYpgEvWS
     * @queryParam relating (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: enKSUfUcZN
     * @queryParam media-of (filter) The id or short id of the entity the result entities should have a media relation to. Example: enKSUfUcZN
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related, entities_related.contents (nested relations)
     * @queryParam per-page The amount of entities per page the result should be the amount of entities on a single page. Example: 6
     * @urlParam model_name If a model name is provided, the results will have the corresponding scope and special defined relations and accesosrs will be available.
     * @responseFile responses/entities.index.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $request->validate($this->queryParamsValidation());
        $lang = $request->get('lang') ?? Config::get('kusikusi_website.langs')[0] ?? '';
        $modelClassName = Entity::getEntityClassName($request->get('of-model') ?? 'Entity');
        $entities = $modelClassName::query()->select('*')
        ->when($request->get('of-model'), function ($q) use ($request) {
            return $q->ofModel($request->get('of-model'));
        })
        ->when($request->exists('only-published') && ($request->get('only-published') === 'true' || $request->get('only-published') === ''), function ($q) use ($request) {
            return $q->isPublished();
        })
        ->when($request->get('children-of'), function ($q) use ($request) {
            return $q->childrenOf($request->get('children-of'));
        });
        $entities = $entities
            ->paginate($request
            ->get('per-page') ? intval($request->get('per-page')) : Config::get('kusikusi_api.page_size', 100))
            ->withQueryString();
        return $entities;
    }
    private function queryParamsValidation() {
        return [
            'children-of' => self::ID_RULE,
            'parent-of' => self::ID_RULE,
            'ancestors-of' => self::ID_RULE,
            'descendants-of' => self::ID_RULE,
            'siblings-of' => self::ID_RULE,
            'related-by' => self::ID_RULE_WITH_FILTER,
            'relating' => self::ID_RULE_WITH_FILTER,
            'media-of' => self::ID_RULE,
            'of-model' => self::MODEL_RULE,
            'only-published' => 'in:true,false',
        ];
    }
}
