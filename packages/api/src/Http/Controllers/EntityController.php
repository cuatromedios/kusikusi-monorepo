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

    public function index(Request $request, $model_name = null)
    {
        $entities = Entity::select('*');
        $entities = $entities->paginate($request->get('per-page') ? intval($request->get('per-page')) : Config::get('kusikusi_api.page_size', 100))
            ->withQueryString();
        return $entities;
    }

}
