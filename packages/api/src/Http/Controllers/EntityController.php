<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller as BaseController;
use Kusikusi\Models\Entity;

class EntityController extends BaseController
{

    const ID_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]+$/';
    const ID_RULE_WITH_FILTER = 'string|min:1|max:64|regex:/^[A-Za-z0-9_-]+:?[a-z0-9]*$/';
    const MODEL_RULE = 'string|min:1|max:32|regex:/^[A-Z][A-Za-z0-9]+$/';
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
     * @queryParam of-model (filter) The name of the model the entities should be, this will also call the query using the given model and not the default Entity model. Example: Medium, Page
     * @queryParam only-published (filter) Get only published, not deleted entities, true if not set. Example: true
     * @queryParam children-of (filter) The id or short id of the entity the result entities should be child of. Example: home
     * @queryParam parent-of (filter) The id or short id of the entity the result entities should be parent of (will return only one). Example: 8fguTpt5SB
     * @queryParam ancestor-of (filter) The id or short id of the entity the result entities should be ancestor of. Example: enKSUfUcZN
     * @queryParam descendant-of (filter) The id or short id of the entity the result entities should be descendant of. Example: xAaqz2RPyf
     * @queryParam siblings-of (filter) The id or short id of the entity the result entities should be siblings of. Example: _tuKwVy8Aa
     * @queryParam related-by (filter) The id or short id of the entity the result entities should have been called by using a relation. Can be added a filter to a kind of relation for example: theShortId:category. The ancestor kind of relations are discarted unless are explicity specified. Example: ElFYpgEvWS
     * @queryParam relating (filter) The id or short id of the entity the result entities should have been a caller of using a relation. Can be added a filder to a kind o relation for example: shortFotoId:medium to know the entities has caller that medium. The ancestor kind of relations are discarted unless are explicity specified. Example: enKSUfUcZN
     * @queryParam media-of (filter) The id or short id of the entity the result entities should have a media relation to. Example: enKSUfUcZN
     * @queryParam select A comma separated list of fields of the entity to include. It is possible to flat the properties json column using a dot syntax. Example: id,model,properties.price
     * @queryParam order-by A comma separated lis of fields to order by. Example: model,properties.price:desc,content.title
     * @queryParam per-page The amount of entities per page the result should be the amount of entities on a single page. Example: 6
     * @queryParam page The number of page to display, 1 by default
     * @queryParam where A comma separated list of conditions to met, Example: created_at>2020-01-01,properties.isImage,properties.format:png,model:medium
     * 
     * @queryParam with A comma separated list of relationships should be included in the result. Example: media,contents,entities_related, entities_related.contents (nested relations)
     * @responseFile responses/entities.index.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $validatedParams = $request->validate($this->queryParamsValidation());
        $lang = $request->get('lang') ?? Config::get('kusikusi_website.langs')[0] ?? '';
        $modelClassName = Entity::getEntityClassName($request->get('of-model') ?? 'Entity');
        
        $entities = $modelClassName::query();
        $entities = $this->addSelects($entities, $request, $lang, $modelClassName);
        $entities = $this->addScopes($entities, $request, $lang, $modelClassName);
        $entities = $this->addWheres($entities, $request, $lang);
        $entities = $this->addOrders($entities, $request, $lang);

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
            'only-published' => Rule::in(['true', 'false', '']),
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
            preg_match('/([:!><][:]?)/', $item, $operator_matches);
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
                "option" => \is_numeric($itemOption) ? (float) $itemOption : $itemOption,
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
                        $q->orderByContent($this->dotsToArrows($order['field']), $this->ascOrDesc($order['option']), $lang);
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
                        $q->orderBy($this->dotsToArrows($order['fullField']), $this->ascOrDesc($order['option']));
                        break;
                    case 'content':
                    case 'contents':
                        $q->orderByContent($this->dotsToArrows($order['field']), $this->ascOrDesc($order['option']), $lang);
                        break;
                    default:
                        $q->where($where['field'], $where['operator'], $where['option']);
                        break;
                }
            }
        });
        return $entities;
    }
}
