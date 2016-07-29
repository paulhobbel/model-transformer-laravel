<?php

namespace Dizzy\Transformer;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class AbstractTransformer
{
    protected $options;

    /**
     * Initialize transformer.
     *
     * @param $options
     */
    private function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * Transform a Model or a Collection.
     *
     * @param mixed $modelOrCollection
     * @param array $options
     *
     * @return mixed
     */
    static function transform($modelOrCollection, $options = [])
    {
        $static = new static($options);

        if ($modelOrCollection instanceof Collection) {
            return $modelOrCollection->map(function(Model $item) use ($static) {
                return $static->transformModel($item);
            })->toArray();
        }

        return $static->transformModel($modelOrCollection);
    }

    /**
     * Check if the model instance is loaded from the provided pivot table.
     *
     * @param Model $item
     * @param string $tableName
     *
     * @return bool
     */
    protected function isLoadedFromPivotTable(Model $item, $tableName)
    {
        return $item->pivot && $item->pivot->getTable() == $tableName;
    }

    /**
     * Check if the provided relationship is loaded.
     *
     * @param Model $item
     * @param string $relationshipName
     *
     * @return bool
     */
    protected function isRelationshipLoaded(Model $item, $relationshipName)
    {
        return $item->relationLoaded($relationshipName);
    }

    /**
     * Transform the provided model.
     *
     * @param Model $item
     * @return array
     */
    protected function transformModel(Model $item) {
        return $item->toArray();
    }
}