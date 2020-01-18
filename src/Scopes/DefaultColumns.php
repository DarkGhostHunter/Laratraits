<?php

namespace DarkGhostHunter\Laratraits\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DefaultColumns
 * ---
 * This scopes injects a "select" into the Eloquent Query Builder with a given list of columns, which can be later
 * be overridden by the query itself. This allows the queried record to only select some columns instead of all,
 * which without can become problematic when tidying up memory consumption and data retrieved for each Model.
 *
 * @package DarkGhostHunter\Laratraits\Scopes
 */
class DefaultColumns implements Scope
{
    /**
     * The Columns to select
     *
     * @var array
     */
    protected $defaultColumns;

    /**
     * Create a new DefaultColumns instance.
     *
     * @param  array  $defaultColumns
     */
    public function __construct(array $defaultColumns)
    {
        $this->defaultColumns = $defaultColumns;
    }

    /**
     * @inheritDoc
     */
    public function apply(Builder $builder, Model $model)
    {
        return $this->defaultColumns === []
            ? $builder
            : $builder->select($this->defaultColumns);
    }
}
