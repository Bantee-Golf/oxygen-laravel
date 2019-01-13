<?php

namespace EMedia\Oxygen\Http\Controllers\Traits;

use EMedia\Formation\Builder\Formation;
use Illuminate\Http\Request;

trait HasHttpCRUD
{

	protected $dataRepo;
	protected $model;
	protected $entityPlural;
	protected $entitySingular;

	protected function indexRouteName()
	{
		// return 'manage.$resourceName.index';
	}

	protected function indexViewName()
	{
		// return 'manage.$resourceName.index';
	}

	protected function formViewName()
	{
		// return 'oxygen::defaults.formation-form';
	}

	public function index()
	{
		if (empty($this->entityPlural))
			throw new \InvalidArgumentException("'entityPlural' value of the controller is not set.");

		$data = [
			'pageTitle' => $this->entityPlural,
			'allItems' => $this->dataRepo->search(),
		];

		$viewName = $this->indexViewName();
		if (empty($viewName)) {
			throw new \InvalidArgumentException("'indexViewName' is empty. Override indexViewName() method in controller.");
		}

		return view($viewName, $data);
	}

	public function create()
	{
		if (empty($this->entitySingular))
			throw new \InvalidArgumentException("'entitySingular' value of the controller is not set.");

		$data = [
			'pageTitle' => 'Add new ' . $this->entitySingular,
			'entity' => $this->model,
			'form' => new Formation($this->model),
		];

		$viewName = $this->formViewName();
		if (empty($viewName)) {
			throw new \InvalidArgumentException("'indexViewName' is empty. Override indexViewName() method in controller.");
		}

		return view($viewName, $data);
	}

	public function store(Request $request)
	{
		return $this->storeOrUpdateRequest($request);
	}

	public function edit($id)
	{
		if (empty($this->entitySingular))
			throw new \InvalidArgumentException("'entityPlural' value of the controller is not set.");

		$entity = $this->dataRepo->find($id);
		$form = new Formation($entity);

		$data = [
			'pageTitle' => 'Edit ' . $this->entitySingular,
			'entity' => $entity,
			'form' => $form,
		];

		$viewName = $this->formViewName();
		if (empty($viewName)) {
			throw new \InvalidArgumentException("'indexViewName' is empty. Override indexViewName() method in controller.");
		}

		return view($viewName, $data);
	}

	public function update(Request $request, $id)
	{
		return $this->storeOrUpdateRequest($request, $id);
	}

	protected function storeOrUpdateRequest(Request $request, $id = null)
	{
		if (empty($this->indexRouteName()))
			throw new \InvalidArgumentException("'indexRouteName()' returns an empty value.");

		$this->validate($request, $this->model->getRules());

		$entity = $this->dataRepo->fillFromRequest($request, $id);

		return redirect()->route($this->indexRouteName());
	}

	public function destroy($id)
	{
		// TODO: for safety, leave the controller to implement this
	}

}