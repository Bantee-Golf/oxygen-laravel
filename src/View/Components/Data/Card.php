<?php

namespace EMedia\Oxygen\View\Components\Data;

use Illuminate\View\Component;

class Card extends Component
{

	public $title;

	/**
	 * Create a new component instance.
	 *
	 * @return void
	 */
	public function __construct($title)
	{
		$this->title = $title;
	}

	/**
	 * Get the view / contents that represent the component.
	 *
	 * @return \Illuminate\Contracts\View\View|\Closure|string
	 */
	public function render()
	{
		return view('oxygen::components.data.card');
	}

}
