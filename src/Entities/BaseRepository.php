<?php

namespace EMedia\Oxygen\Entities;

use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository {

	private $model;

	public function __construct(Model $model)
	{
		$this->model = $model;
	}

	public function all($relationships = [])
	{
		$query = $this->model->select();
		foreach ($relationships as $relation)
		{
			$query->with($relation);
		}
		return $query->get();
	}

	public function paginate($perPage, $relationships = [], $filters = [], $orFilters = [])
	{
		$query = $this->model->select();
		foreach ($relationships as $relation)
		{
			$query->with($relation);
		}
		if ( ! empty($filters))
		{
			foreach ($filters as $filterField => $filterValue)
			{
				$query->where($filterField, 'LIKE', '%' . $filterValue . '%');
			}
		}
		if (count($orFilters) > 0)
		{
			$query->where(function ($q) use ($orFilters)
			{
				foreach ($orFilters as $filterField => $filterValue)
				{
					$q->orWhere($filterField, 'LIKE', '%' . $filterValue . '%');
				}
			});
		}
		$query->orderBy('id', 'desc');
		return $query->paginate($perPage);
	}

	public function create($input)
	{
		$model = new $this->model();
		$model->fill($input);
		$model->save();
		return $model;
	}

	public function findOrCreate($id, $input)
	{
		// update or create new record
		if (empty($id))
		{
			$model = $this->create($input);
		}
		else
		{
			$model = $this->find($id);
			$this->update($model, $input);
		}
		return $model;
	}

	public function find($id, $relationships = [])
	{
		$query = $this->model->select();
		foreach ($relationships as $relation)
		{
			$query->with($relation);
		}
		return $query->find($id);
	}

	public function update($model, $updateData)
	{
		$model->fill($updateData);
		$model->save();
		return $model;
	}

	public function save($model)
	{
		return $model->save();
	}

	public function delete($id)
	{
		$model = $this->model->find($id);
		$model->delete();
		return true;
	}

	public function allAsList()
	{
		$allItems = $this->all();
		return $this->convertToList($allItems);
	}

	public function convertToList($collection)
	{
		$itemsData = [];
		foreach ($collection as $item)
		{
			$itemsData[] = ['value' => $item->id, 'name' => $item->name];
		}
		return $itemsData;
	}
}