<?php

namespace App\Api\Foundation\Repository\Eloquent;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;
use App\Api\Foundation\Repository\EloquentRepositoryInterface;
use Carbon\Carbon;

class BaseRepository implements EloquentRepositoryInterface
{
    public $connection;

    public function __construct(Model $model)
    {
        $this->connection = $model;
    }

    public function getAll()
    {
        return $this->connection->query()->get();
    }

    public function getDataById(int $id)
    {
        return $this->connection->query()->where('id', $id)->first();
    }

    public function getDataByUuid(string $uuid)
    {
        return $this->connection->query()->where('uuid', $uuid)->first();
    }

    public function insert(array $data)
    {

        return $this->connection->query()->create($data);
    }

    public function update(array $data, int $id): int
    {
        return $this->connection->query()->where('id', $id)->update($data);
    }

    public function destroy(int $id)
    {
        return $this->connection->query()->where('id', $id)->delete();
    }

}
