<?php

namespace RotHub\Laravel\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use RotHub\Laravel\Models\Shorten;
use RotHub\Laravel\Services\Service;

class CurdService extends Service
{
    /**
     * @var Model 模型.
     */
    protected $model;
    /**
     * @var array|string 字段.
     */
    protected $columns = '*';

    /**
     * 设置模型.
     * 
     * @param string $value 模型.
     * @return static
     */
    public function setModel(string $value): static
    {
        $this->model = $value;

        return $this;
    }

    /**
     * 设置字段.
     * 
     * @param array|string $value 字段.
     * @return static
     */
    public function setColumns(array|string $value): static
    {
        $this->columns = $value;

        return $this;
    }

    /**
     * 添加.
     * 
     * @param array $input 参数.
     * @return array
     */
    public function add(array $input): array
    {
        $model = $this->model::create($input);

        return ['id' => $model->id];
    }

    /**
     * 获取.
     * 
     * @param array $input 参数.
     * @return array
     */
    public function get(array $input): array
    {
        return $this->model::findOrFail($input['id'], $this->columns)
            ->toArray();
    }

    /**
     * 更新.
     * 
     * @param array $input 参数.
     * @return void
     */
    public function set(array $input): void
    {
        $this->model::findOrFail($input['id'])
            ->updateOrFail($input);
    }

    /**
     * 删除.
     * 
     * @param array $input 参数.
     * @return int
     */
    public function del(array $input): int
    {
        return $this->model::destroy($input['ids']);
    }

    /**
     * 搜索.
     * 
     * @param array $input 参数.
     * @return LengthAwarePaginator
     */
    public function search(array $input = []): LengthAwarePaginator
    {
        return $this->model::query()
            ->when(...Shorten::when($input, 'status'))
            ->when(...Shorten::sort($input))
            ->orderBy(config('rothub.INDEX_AT'), 'asc')
            ->orderBy($this->model::CREATED_AT, 'desc')
            ->select($this->columns)
            ->paginate(...Shorten::page($input));
    }

    /**
     * 全部.
     * 
     * @param array $input 参数.
     * @return Collection
     */
    public function all(array $input = []): Collection
    {
        return $this->model::query()
            ->when(...Shorten::when($input, 'status'))
            ->when(...Shorten::sort($input))
            ->orderBy(config('rothub.INDEX_AT'), 'asc')
            ->orderBy($this->model::CREATED_AT, 'desc')
            ->select($this->columns)
            ->get();
    }
}
