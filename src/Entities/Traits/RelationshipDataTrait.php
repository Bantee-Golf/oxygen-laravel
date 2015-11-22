<?php

namespace EMedia\Oxygen\Entities\Traits;

trait RelationshipDataTrait
{

	public function getRules()
	{
		if (isset($this->rules)) return $this->rules;

		return [];
	}

	/**
	 *
	 * Keep track of Many to Many relations of this model
	 *
	 * @return array
	 */
	public function getManyToManyRelations()
	{
		if (isset($this->manyToManyRelations)) return $this->manyToManyRelations;

		return [];
	}

	public function getHasManyRelations()
	{
		if (isset($this->hasManyRelations)) return $this->hasManyRelations;

		return [];
	}

	public function getFillablePivots()
	{
		if (isset($this->fillablePivots)) return $this->fillablePivots;

		return [];
	}

}