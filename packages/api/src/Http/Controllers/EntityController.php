<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller as BaseController;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityContent;
use Kusikusi\Models\EntityRoute;
use Kusikusi\Models\EntityRelation;

class EntityController extends BaseController
{

    const ID_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]{1,32}$/';
    const ID_RULE_WITH_FILTER = 'string|min:1|max:64|regex:/^[A-Za-z0-9_-]+:?[a-z0-9]*$/';
    const ENTITY_EXISTS = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]{1,32}$/|exists:entities,id';
    const MODEL_RULE = 'string|min:1|max:32|regex:/^[A-Z][A-Za-z0-9]+$/';
    const TIMEZONED_DATE = 'nullable|date_format:Y-m-d\TH:i:sP|after_or_equal:1000-01-01T00:00:00-12:00|before_or_equal:9999-12-31T23:59:59-12:00';

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
     * @queryParam lang The identification of the language to use for select, where and order by content fields, for example en, es, en-US, etc.
     * @queryParam of-model (filter) The name of the model the entities should be, this will also call the query using the given model and not the default Entity model. Example: Medium, Page
     * @queryParam only-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam children-of (filter) The id of the entity the result entities should be child of. Example: home
     * @queryParam parent-of (filter) The id of the entity the result entities should be parent of (will return only one). Example: 8fguTpt5SB
     * @queryParam ancestor-of (filter) The id of the entity the result entities should be ancestor of. Example: enKSUfUcZN
     * @queryParam descendant-of (filter) The id of the entity the result entities should be descendant of. Example: xAaqz2RPyf
     * @queryParam siblings-of (filter) The id of the entity the result entities should be siblings of. Example: _tuKwVy8Aa
     * @queryParam related-by (filter) The id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: ElFYpgEvWS
     * @queryParam relating (filter) The id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: enKSUfUcZN
     * @queryParam media-of (filter) The id of the entity the result entities should have a media relation to. Example: enKSUfUcZN
     * @queryParam select A comma separated list of fields of the entity to include. It is possible to use a dot syntax the properties and content column. Example: id,model,properties.price,content.title
     * @queryParam order-by A comma separated lis of fields to order by. Example: model,properties.price:desc,content.title
     * @queryParam per-page The amount of entities per page the result should be the amount of entities on a single page. Example: 6
     * @queryParam page The number of page to display, 1 by default
     * @queryParam where A comma separated list of conditions to met, Example: created_at>2020-01-01,content.title:The%20title,properties.format:png,model:medium
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related,route
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validatedParams = $request->validate($this->queryParamsValidation(), $this->queryParamsMessages());
        $lang = $request->get('lang') ?? Config::get('kusikusi_website.langs')[0] ?? '';
        $receivedLang = $request->get('lang') ?? null;
        $modelClassName = Entity::getEntityClassName($request->get('of-model') ?? 'Entity');
        
        $entities = $modelClassName::query();
        $entities = $this->addSelects($entities, $request, $lang, $modelClassName);
        $entities = $this->addScopes($entities, $request, $lang, $modelClassName);
        $entities = $this->addWheres($entities, $request, $lang);
        $entities = $this->addOrders($entities, $request, $lang);
        $entities = $this->addWiths($entities, $request, $receivedLang);

        $entities = $entities
            ->paginate($request
            ->get('per-page') ? intval($request->get('per-page')) : Config::get('kusikusi_api.page_size', 100))
            ->withQueryString();
        return $entities;
    }
/**
     * Retrieve the entity for the given ID.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to show.
     * @queryParam lang The identification of the language to use for select, where and order by content fields, for example en, es, en-US, etc.
     * @queryParam select select A comma separated list of fields of the entity to include. It is possible to use a dot syntax the properties and content column. Example: id,model,properties.price,content.title
     * @queryParam @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related,route
     * @responseFile responses/entities.show.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $entity_id)
    {
        $lang = $request->get('lang') ?? Config::get('kusikusi_website.langs')[0] ?? '';
        $receivedLang = $request->get('lang') ?? null;
        $entityFound = Entity::select('id', 'model')
            ->where('id', $entity_id)
            ->firstOrFail();
        $modelClassName = Entity::getEntityClassName(Str::singular($entityFound->model));
        $entity = $modelClassName::select('id');
        $entity = $this->addSelects($entity, $request, $lang, $modelClassName);
        $entity = $this->addWiths($entity, $request, $receivedLang);
        return $entity->findOrFail($entityFound->id);;
    }

    /**
     * Stores a new entity.
     *
     * @group Entity
     * @authenticated
     * @bodyParam id string Optional. You can set your own ID, a maximum of 32, safe characters: A-Z, a-z, 0-9, _ and -. Default: autogenerated. Example: home
     * @bodyParam model string required The model name, studly case. Example: Page.
     * @bodyParam properties string An object with properties. Example: {"price": 200, "format": "jpg"}
     * @bodyParam view string The name of the view to use. Default: the same name of the model. Example: page
     * @bodyParam langs array An array of languages the entity has active. Example: ["en", "es"]
     * @bodyParam parent_entity_id The id of the parent entity. Example: home
     * @bodyParam visibility A visibility status of an entity. Example: public, draft, private
     * @bodyParam published_at date A timezoned date time the entity should be published. Default: current date time. Example: 2020-02-02T12:00:00-00:00.
     * @bodyParam unpublished_at date A timezoned date time the entity should be published. Default: 9999-12-31 23:59:59. Example: 2020-02-02T12:00:00-06:00.
     * @bodyParam contents array An array of contents to be created for the entity. Example: []
     * @bodyParam relations arrya An array of relations to be created for the entity. Example: "relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]
     * @responseFile responses/entities.create.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate($this->entityPlayloadValidation());
        $payload = $request->only('id', 'model', 'properties', 'view', 'langs', 'parent_entity_id', 'visibility', 'published_at', 'unpublished_at');
        $modelClassName = Entity::getEntityClassName(Str::singular($request->model));
        $entity = new $modelClassName($payload);
        $entity->save();
        if (isset($request->contents)) EntityContent::createFromArray($entity->id, $request->contents);
        if (isset($request->routes)) EntityRoute::createFromArray($entity->id, $request->routes, $entity->model);
        if (isset($request->entities_related)) EntityRelation::createFromArray($entity->id, $request->entities_related);
        $createdEntity = $modelClassName::withContents()->withRoutes()->with('entities_related')->find($entity->id);
        return($createdEntity);
    }

    /**
     * Updates an entity.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to update. Example: home
     * @bodyParam model string required The model name, studly case. Example: Page.
     * @bodyParam properties string An object with properties. Example: {"price": 200, "format": "jpg"}
     * @bodyParam view string The name of the view to use. Default: the same name of the model. Example: page
     * @bodyParam langs array An array of languages the entity has active. Example: ["en", "es"]
     * @bodyParam parent_entity_id The id of the parent entity. Example: home
     * @bodyParam visibility A visibility status of an entity. Example: public, draft, private
     * @bodyParam published_at date A timezoned date time the entity should be published. Default: current date time. Example: 2020-02-02T12:00:00-00:00.
     * @bodyParam unpublished_at date A timezoned date time the entity should be published. Default: 9999-12-31 23:59:59. Example: 2020-02-02T12:00:00-06:00.
     * @bodyParam contents array An array of contents to be created for the entity. Example: [{ "lang": "en", "field": "slug",  "text": "page" }]
     * @bodyParam routes array An array of routes to be created for the entity. Example: [{ "path": "/en", "lang": "en",  "kind": "alias" }]
     * @bodyParam relations array An array of relations to be created for the entity. Example: "relations": [{"called_entity_id": "mf4gWE45pm","kind": "category","position": 2, "tags":["main"]}]
     * @responseFile responses/entities.create.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $entity_id)
    {
        $request->validate($this->entityPlayloadValidation());
        $payload = $request->only('id', 'model', 'properties', 'view', 'langs', 'parent_entity_id', 'visibility', 'published_at', 'unpublished_at');
        $modelClassName = Entity::getEntityClassName(Str::singular($request->model ?? 'Entity'));
        $entity = $modelClassName::withTrashed()->findOrFail($entity_id);
        $entity->fill($payload);
        $entity->save();
        if (isset($request->contents)) EntityContent::createFromArray($entity->id, $request->contents);
        if (isset($request->routes)) EntityRoute::createFromArray($entity->id, $request->routes, $entity->model);
        if (isset($request->entities_related)) EntityRelation::createFromArray($entity->id, $request->entities_related);
        $updatedEntity = $modelClassName::withContents()->withRoutes()->with('entities_related')->find($entity->id);
        return($updatedEntity);
    }

    /**
     * Deletes an entity.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to delete
     * @queryParam hard boolean If the deletion should be a hard delete
     * @responseFile responses/entities.delete.json
     */
    public function destroy(Request $request, $entity_id)
    {
        $request->validate(['hard' => Rule::in(['true', 'false', ''])]);
        if ($request->get('hard') && ($request->get('hard') === 'true' || $request->get('hard') === '')) {
            Entity::where('id', $entity_id)->forceDelete();
            return (["message" => "Entity ${entity_id} was hard deleted"]);
            
        } else {
            Entity::where('id', $entity_id)->delete();
            $entity = Entity::select('id', 'deleted_at')->withTrashed()->findOrFail($entity_id);
            if ( $entity) $entity->makeVisible('deleted_at');
            return($entity);
        }
    }

    /**
     * Restores a soft deleted entity.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to delete
     * @responseFile responses/entities.delete.json
     */
    public function restore(Request $request, $entity_id)
    {
        Entity::where('id', $entity_id)->restore();
        $entity = Entity::select('id', 'deleted_at')->withTrashed()->findOrFail($entity_id);
        if ( $entity) {
            $entity->makeVisible('deleted_at');
        }
        return($entity);
    }


    // Validations
    private function queryParamsValidation() {
        return [
            'children-of' => self::ENTITY_EXISTS,
            'parent-of' => self::ENTITY_EXISTS,
            'ancestors-of' => self::ENTITY_EXISTS,
            'descendants-of' => self::ENTITY_EXISTS,
            'siblings-of' => self::ENTITY_EXISTS,
            'related-by' => self::ENTITY_EXISTS,
            'relating' => self::ENTITY_EXISTS,
            'media-of' => self::ENTITY_EXISTS,
            'of-model' => self::MODEL_RULE,
            'only-published' => Rule::in(['true', 'false', '']),
        ];
    }
    private function queryParamsMessages() {
        $must_exist = 'The entity must exist';
        return [
            'children-of.exists' => $must_exist,
            'parent-of.exists' => $must_exist,
            'ancestors-of.exists' => $must_exist,
            'descendants-of.exists' => $must_exist,
            'siblings-of.exists' => $must_exist,
            'related-by.exists' => $must_exist,
            'relating.exists' => $must_exist,
            'media-of.exists' => $must_exist,
        ];
    }
    private function entityPlayloadValidation() {
        return [
            'model' => 'string|max:32',
            'view' => 'string|max:32',
            'id' => self::ID_RULE,
            'parent_entity_id' => self::ID_RULE,
            'published_at' => self::TIMEZONED_DATE,
            'unpublished_at' => self::TIMEZONED_DATE,
            'is_active' => 'boolean',
            'contents.*.lang' => 'required_with:contents|string',
            'contents.*.field' => 'required_with:contents|string',
            'contents.*.text' => 'required_with:contents|string',
            'routes.*.path' => 'required_with:routes|string',
            'routes.*.lang' => 'required_with:routes|string',
            'routes.*.kind' => 'required_with:routes|string',
            'relations.*.called_entity_id' => 'required_with:relations|'.self::ID_RULE,
            'relations.*.kind' => 'required_with:relations|string'
        ];
    }
    /**
     * Get the parts of a param string in the query with the format table.field:option,table.field:option
     * @param $params String The url string of a param, for example model,content.title,properties.size:desc
     * @return Array With the parts like [["table"=>"entities", "field"=>"model", "option":"desc"],[...]]
     */
    private function getParts(String $params) {
        $result = [];
        $paramItems = explode(',', $params);
        foreach($paramItems as $item) {
            preg_match('/([:!><][:>]?)/', $item, $operator_matches);
            $operator = $operator_matches[0] ?? null;
            $itemParts = $operator ? explode($operator, $item) : [$item];
            $itemFullField = $itemParts[0];
            $itemOption = $itemParts[1] ?? null;
            $itemFieldParts = explode('.', $itemFullField);
            $itemTable = isset($itemFieldParts[1]) ? $itemFieldParts[0] : null;
            $itemField = isset($itemFieldParts[1]) ? Str::after($itemFullField, $itemTable.'.') : $itemFieldParts[0];
            $result[] = [
                "table" => $itemTable,
                "field" => $itemField,
                "option" => is_numeric($itemOption) ? (float) $itemOption : ($itemOption === 'null' ? null : $itemOption),
                "operator" => str_replace(':', '=', $operator),
                "fullField" => $itemFullField
            ];
        }
        return $result;
    }

    private function ascOrDesc($option) {
        return Str::lower($option) === 'desc' ? 'DESC' : 'ASC';
    }
    private function dotsToArrows($field) {
        return str_replace('.', '->', $field);
    }


    /**
     * Process the request to know for select query parameter and add the corresponding select statments
     *
     * @param $query
     * @param $request
     * @return mixed
     */
    private function addSelects($query, $request, $lang, $modelClassName) {
        // Selects
        $query->when(!$request->exists('select'), function ($q) use ($request) {
            return $q->select('entities.*');
        })
        ->when($request->get('select'), function ($q) use ($request, $lang, $modelClassName) {
            $items = $this->getParts($request->get('select'));
            foreach ($items as $item) {
                $appendContents = [];
                $appendContent = [];
                if ($item['table'] === 'properties') {
                    $q->addSelect($item['table'].'->'.$this->dotsToArrows($item['field']));
                } else if ($item['table'] ===  'contents') {
                    $appendContents[] =$item['field'];
                } else if ($item['table'] ===  'content') {
                    $appendContent[] = $item['field'];
                } else if ($item['field'] === "contents") {
                    $q->withContents($lang);
                } else if ($item['field'] === "content") {
                    $q->withContent($lang);
                } else if ($item['field']) {
                    $q->addSelect($item['field']);
                }
                if (count($appendContents) > 0) {
                    $q->withContents($lang, $appendContents);
                }
                if (count($appendContent) > 0) {
                    $q->withContent( $lang, $appendContent);
                }
            }
            return $q;
        });
        return $query;
    }
    private function addScopes($entities, $request) {
        $entities
        ->when($request->get('of-model'), function ($q) use ($request) {
            return $q->ofModel($request->get('of-model'));
        })
        ->when($request->get('only-published') && (($request->get('only-published') === 'true' || $request->get('only-published')) === ''), function ($q) use ($request) {
            // return $q->isPublished();
        })
        ->when($request->get('children-of'), function ($q) use ($request) {
            return $q->childrenOf($request->get('children-of'));
        })
        ->when($request->get('parent-of'), function ($q) use ($request) {
            return $q->parentOf($request->get('parent-of'));
        })
        ->when($request->get('ancestors-of'), function ($q) use ($request) {
            return $q->ancestorsOf($request->get('ancestors-of'));
        })
        ->when($request->get('descendants-of'), function ($q) use ($request) {
            return $q->descendantsOf($request->get('descendants-of'));
        })
        ->when($request->get('siblings-of'), function ($q) use ($request) {
            return $q->siblingsOf($request->get('siblings-of'));
        })
        ->when($request->get('related-by'), function ($q) use ($request) {
            $values = explode(":", $request->get('related-by'));
            if (isset($values[1])) {
                return $q->relatedBy($values[0], $values[1]);
            } else {
                return $q->relatedBy($values[0]);
            }

        })
        ->when($request->get('relating'), function ($q) use ($request) {
            $values = explode(":", $request->get('relating'));
            if (isset($values[1])) {
                return $q->relating($values[0], $values[1]);
            } else {
                return $q->relating($values[0]);
            }

        })
        ->when($request->get('media-of'), function ($q) use ($request) {
            return $q->mediaOf($request->get('media-of'));
        });
        return $entities;
    }
    private function addOrders($entities, $request, $lang) {
        $entities->when($request->get('order-by'), function ($q) use ($request, $lang) {
            $ordersBy = $this->getParts($request->get('order-by'));
            foreach ($ordersBy as $order) {
                switch ($order['table']) {
                    case 'properties':
                        $q->orderBy($this->dotsToArrows($order['fullField']), $this->ascOrDesc($order['option']));
                        break;
                    case 'content':
                    case 'contents':
                        $q->orderByContent($order['field'], $this->ascOrDesc($order['option']), $lang);
                        break;
                    default:
                        $q->orderBy($order['field'], $this->ascOrDesc($order['option']));
                        break;
                }
            }
        });
        return $entities;
    }
    private function addWheres($entities, $request, $lang) {
        $entities->when($request->get('where'), function ($q) use ($request, $lang) {
            $wheres = $this->getParts($request->get('where'));
            foreach ($wheres as $where) {
                switch ($where['table']) {
                    case 'properties':
                        $q->where($this->dotsToArrows($where['fullField']), $where['operator'], $where['option']);
                        break;
                    case 'content':
                    case 'contents':
                        $q->whereContent($where['field'], $where['operator'], $where['option'], $lang);
                        break;
                    default:
                        $q->where($where['field'], $where['operator'], $where['option']);
                        break;
                }
            }
        });
        return $entities;
    }
    private function addWiths($entities, $request, $lang) {
        $entities->when($request->get('with'), function ($q) use ($request, $lang) {
            $withs = $this->getParts($request->get('with'));
            foreach ($withs as $with) {
                switch($with['field']) {
                    case 'routes':
                        $q->withRoutes($lang);
                        break;
                    case 'route':
                        $q->withRoute($lang);
                        break;
                    case 'medium':
                        $q->withMedium($with['option'], null, $lang);
                        break;
                    case 'media':
                        $q->withMedia($with['option'], null, $lang);
                        break;
                    case 'contents':
                        $q->withContents($lang);
                        break;
                    case 'content':
                        $q->withContent($lang);
                        break;
                    default:
                        $q->with($with['field']);
                }
            }
        });
        return $entities;
    }
}
