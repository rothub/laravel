<?php

namespace RotHub\Laravel\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use RotHub\Laravel\Scopes\SoftDeletingScope;

trait SoftDeletesTrait
{
    use SoftDeletes;

    /**
     * @var int 删除后值.
     */
    public $deletedValue = -1;
    /**
     * @var int 恢复后值.
     */
    public $restoredValue = 0;

    /**
     * @inheritdoc
     */
    public static function bootSoftDeletes()
    {
        static::addGlobalScope(new SoftDeletingScope);
    }

    /**
     * @inheritdoc
     */
    public function initializeSoftDeletes()
    {
        // if (!isset($this->casts[$this->getDeletedAtColumn()])) {
        //     $this->casts[$this->getDeletedAtColumn()] = 'integer';
        // }
    }

    /**
     * @inheritdoc
     */
    protected function runSoftDelete()
    {
        $query = $this->setKeysForSaveQuery($this->newModelQuery());

        $time = $this->freshTimestamp();

        $columns = [$this->getDeletedAtColumn() => $this->deletedValue];

        $this->{$this->getDeletedAtColumn()} = $this->deletedValue;

        if (!is_null($this->getUpdatedAtColumn())) {
            $this->{$this->getUpdatedAtColumn()} = $time;

            $columns[$this->getUpdatedAtColumn()] = $this->fromDateTime($time);
        }

        $query->update($columns);

        $this->syncOriginalAttributes(array_keys($columns));
    }

    /**
     * @inheritdoc
     */
    public function restore()
    {
        // If the restoring event does not return false, we will proceed with this
        // restore operation. Otherwise, we bail out so the developer will stop
        // the restore totally. We will clear the deleted timestamp and save.
        if ($this->fireModelEvent('restoring') === false) {
            return false;
        }

        $this->{$this->getDeletedAtColumn()} = $this->restoredValue;

        // Once we have saved the model, we will fire the "restored" event so this
        // developer will do anything they need to after a restore operation is
        // totally finished. Then we will return the result of the save call.
        $this->exists = true;

        $result = $this->save();

        $this->fireModelEvent('restored', false);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function trashed()
    {
        return $this->{$this->getDeletedAtColumn()} === $this->deletedValue;
    }
}
