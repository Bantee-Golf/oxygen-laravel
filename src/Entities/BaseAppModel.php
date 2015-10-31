<?php

namespace EMedia\Oxygen\Entities;

use Illuminate\Database\Eloquent\Model;

abstract class BaseAppModel extends Model {

	protected $manyToManyRelations = [];
	protected $hasManyRelations	   = [];
	protected $hidden	= ['created_at', 'updated_at', 'deleted_at'];
	protected $searchable = [];

	protected $rules = [];

	public function getRules()
	{
		return $this->rules;
	}

	public function searchable()
    {
        return $this->searchable;
    }

	public function getIdAttribute($data)
	{
		return (int)$data;
	}

	/**
	 *
	 * Keep track of Many to Many relations of this model
	 *
	 * @return array
	 */
	public function getManyToManyRelations()
	{
		return $this->manyToManyRelations;
	}

	public function getHasManyRelations()
	{
		return $this->hasManyRelations;
	}

	public function getFillablePivots()
	{

	}

}