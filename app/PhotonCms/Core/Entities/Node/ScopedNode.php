<?php

namespace Photon\PhotonCms\Core\Entities\Node;

use Illuminate\Database\Eloquent\Model;

class ScopedNode extends Node
{
    /**
     * Used for Nested Set list
     *
     * @var string
     */
    protected $scope = 'scope_id';
    protected $scoped = ['scope_id'];

    /**
     * Children relation module
     *
     * @param $model
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function getScopedChildren($model)
    {
        return $this->hasMany(get_class($model), $model->getScopedColumnName())->orderBy($model->getOrderColumnName());
    }

    /**
     * Parent relation with module
     *
     * @param $model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getScopedParent($model)
    {
        return $this->belongsTo(get_class($model),
            $model->getScopedColumnName())->orderBy($model->getOrderColumnName());
    }

    /**
     * Get the parent column name.
     *
     * @return string
     */
    public function getScopedColumnName()
    {
        return $this->scope;
    }

    /**
     * Sets scope for a node and all of its children.
     *
     * Used for setting a hierarchical scope over nodes.
     *
     * @param int|null $id
     * @return Node
     */
    public function setScope(Model $scope)
    {
        $class = get_class($this);
        $class = new $class();
        $rightMostRoot = $class->where($this->scope, "=", $scope->id)
            ->whereNull($this->parentColumn)
            ->orderBy('rgt', 'desc')
            ->first();

        $this->descendants()->update([$this->scope => $scope->id]);
        $this->{$this->scope} = $scope->id;
        $this->{$this->parentColumn} = null;
        if($rightMostRoot) {
            $this->lft = $rightMostRoot->rgt + 1;
            $this->rgt = $rightMostRoot->rgt + 2;
        }
        $this->save();

        self::rebuild(true);

        return $this;
    }

    /**
     * Unsets the scope of a node and all of its children.
     *
     * @param int|null $id
     * @return Node
     */
    public function unsetScope()
    {
        $class = get_class($this);
        $class = new $class();
        $rightMostRoot = $class->whereNull($this->scope)
            ->whereNull($this->parentColumn)
            ->orderBy('rgt', 'desc')
            ->first();

        $this->descendants()->update([$this->scope => null]);
        $this->{$this->scope} = null;
        $this->{$this->parentColumn} = null;
        if($rightMostRoot) {
            $this->lft = $rightMostRoot->rgt + 1;
            $this->rgt = $rightMostRoot->rgt + 2;
        }
        $this->save();
        self::rebuild(true);

        return $this;
    }
}