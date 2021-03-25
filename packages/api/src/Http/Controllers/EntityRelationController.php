<?php

namespace Kusikusi\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller as BaseController;
use Kusikusi\Models\Entity;
use Kusikusi\Models\EntityRelation;

class EntityRelationController extends BaseController
{

    const ID_RULE = 'string|min:1|max:32|regex:/^[A-Za-z0-9_-]{1,32}$/';

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
     * List relations of an entity, this is an alias of /entities?related-by={entity_id}.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to create relations to.
     */
    public function index(Request $request, $entity_id)
    {
        $request->merge([
            'related-by' => $entity_id,
            'order-by' => "relation.position"
        ]);
        $entityController = new EntityController();
        return $entityController->index($request);
    }
    
    /**
     * Stores a new entity relation, or replace it if already exist.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to create relations to.
     * @urlParam called_entity_id string Required. The the id of the entity to relate
     * @urlParam kind string Required. The type of relation. Example: medium, category, author...
     * @bodyParam position number Optional. A non signed integer to set the position of the relation in reference to other relations. Example 0, 1, 2
     * @bodyParam depth number Optional. A non signed integer to set the depth of the relation in reference to the entity. Example 0, 1, 2
     * @bodyParam tags array Optional. An array of strings to tag the relation. Example icon, main
     * @responseFile responses/entities.create.json
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $entity_id, $called_entity_id, $kind)
    {
        $request->validate([
            'position' => 'integer',
            'depth' => 'integer',
            'tags.*' => 'string'
        ]);
       $relation = EntityRelation::create(array_merge(
           $request->only('position', 'depth', 'tags'),
           [
            "caller_entity_id" => $entity_id,
            "called_entity_id" => $called_entity_id,
            "kind" => $kind
           ]
        ));
        return($relation);
    }


    /**
     * Deletes a relation.
     *
     * @group Entity
     * @authenticated
     * @urlParam entity_id The id of the entity to delete a relations to.
     * @urlParam called_entity_id string Required. The the id of the entity related
     * @urlParam kind string Required. The type of relation. Example: medium, category, author...
     */
    public function destroy(Request $request, $entity_id, $called_entity_or_relation_id, $kind = null)
    {
        if ($kind === null) {
            $deletion = EntityRelation::where('relation_id', $called_entity_or_relation_id)->delete();
        } else {
            $deletion = EntityRelation::where('caller_entity_id', $entity_id)
            ->where('called_entity_id', $called_entity_or_relation_id)
            ->where('kind', $kind)
            ->delete();
        }
        
        return ([ "deletes" => $deletion]);
    }

    /**
     * Reorders an array of relations
     *
     * Receive an array of relation ids, and sets the individual position to its index in the array.
     *
     * @group Entity
     * @authenticated
     * @bodyParam relation_ids array required An array of relation ids to reorder. Example [30, 31, 32, 33]
     * @return \Illuminate\Http\JsonResponse
     */
    public function reorder(Request $request, $entity_id)
    {
        $request->validate([
            'relation_ids' => 'required',
            'relation_ids.*' => (new EntityRelation)->getKeyType()
        ]);
        $relations = EntityRelation::select('relation_id', 'position')->where('caller_entity_id', $entity_id)->find($request['relation_ids']);
        for ($r = 0; $r < $relations->count(); $r++) {
            
            $relations[$r]->position = array_search($relations[$r]->relation_id, $request['relation_ids']) ?? 0;
            $relations[$r]->save();
        }
        return(["data" => $relations]);
    }
}
