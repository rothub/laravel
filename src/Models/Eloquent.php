<?php

namespace RotHub\Laravel\Models;

use RotHub\Laravel\Models\Builder;
use Illuminate\Database\Eloquent\Model as LaravelModel;

abstract class Eloquent extends LaravelModel
{
    /**
     * 软删除字段.
     */
    const DELETED_AT = 'status';

    /**
     * @var bool 自动维护时间戳.
     */
    public $timestamps = true;

    /**
     * @var string 日期列的存储格式.
     */
    protected $dateFormat = 'Y-m-d H:i:s';
    /**
     * @var array 不可批量赋值的属性.
     */
    protected $guarded = [];
    /**
     * @var array 模型的默认属性值.
     */
    protected $attributes = [
        'status' => 1,
    ];
    /**
     * @var array 数组中的属性会被隐藏.
     */
    protected $hidden = [
        'domain',
        'created_by',
        'updated_by',
        'tenant_id',
    ];

    /**
     * @inheritdoc
     */
    public function getCasts()
    {
        $attrs = $this->getAttributes();

        foreach ($attrs as &$value) {
            $value = 'string';
        }

        $this->casts = array_merge($attrs, $this->casts);

        return parent::getCasts();
    }

    /**
     * @inheritdoc
     */
    public function fill(array $attributes)
    {
        foreach ($attributes as $key => &$value) {
            if (is_null($value)) {
                unset($attributes[$key]);
            }
        }

        return parent::fill($attributes);
    }

    /**
     * @inheritdoc
     */
    public function newEloquentBuilder($query)
    {
        return new Builder($query);
    }

    /**
     * @inheritdoc
     */
    protected function serializeDate($value)
    {
        $nil = config('rothub.DATETIME_NIL');

        $date = $value->format($this->getDateFormat());

        return $date === $nil ? '' : $date;
    }
}
