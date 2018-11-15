<?php

namespace App\Entities\Files;

use App\Entities\BaseRepository;

class FilesRepository extends BaseRepository
{

	public function __construct(File $model)
	{
		parent::__construct($model);
	}

	public function findByUuid($uuid)
	{
		return File::where('uuid', $uuid)->first();
	}

	public function findByKey($key)
	{
		return File::where('key', $key)->first();
	}

}