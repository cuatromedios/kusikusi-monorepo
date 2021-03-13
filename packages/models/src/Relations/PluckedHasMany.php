<?php 

namespace Kusikusi\Relations;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Collection;

class PluckedHasMany extends HasMany
{
    /**
     * The pluck value.
     *
     * @var string
     */
    protected $pluckValue;

    /**
     * The pluck key.
     *
     * @var string
     */
    protected $pluckKey;


    /**
     * Create a new has one or many relationship instance.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  \Illuminate\Database\Eloquent\Model  $parent
     * @param  string  $foreignKey
     * @param  string  $localKey
     * @return void
     */
    public function __construct(\Illuminate\Database\Eloquent\Builder $query, \Kusikusi\Models\Entity $parent, $foreignKey, $localKey, $pluckValue, $pluckKey)
    {
        $this->pluckValue = $pluckValue;
        $this->pluckKey = $pluckKey;

        parent::__construct($query, $parent, $foreignKey, $localKey);
    }

    /**
     * Match the eagerly loaded results to their many parents.
     *
     * @param  array  $models
     * @param  \Illuminate\Database\Eloquent\Collection  $results
     * @param  string  $relation
     * @param  string  $type
     * @return array
     */
    protected function matchOneOrMany(array $models, Collection $results, $relation, $type)
    {
        $dictionary = $this->buildDictionary($results);

        // Once we have the dictionary we can simply spin through the parent models to
        // link them up with their children using the keyed dictionary to make the
        // matching very convenient and easy work. Then we'll just return them.
        foreach ($models as $model) {
            if (isset($dictionary[$key = $model->getAttribute($this->localKey)])) {
                $model->setRelation(
                    $relation, $this->getRelationValue($dictionary, $key, $type)->pluck($this->pluckValue, $this->pluckKey)
                );
            }
        }

        return $models;
    }

}