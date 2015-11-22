<?php

namespace EMedia\Oxygen\Entities\Traits;

trait SearchableTrait
{

	public function searchable()
	{
		if (isset($this->searchable)) return $this->searchable;

		return [];
	}

}