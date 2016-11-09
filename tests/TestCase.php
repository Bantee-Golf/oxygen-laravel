<?php


abstract class TestCase extends PHPUnit_Framework_TestCase
{

	public function setUp()
	{
		$this->setUpDatabase();
		$this->migrateTables();
	}

	protected function setUpDatabase()
	{

	}

	protected function migrateTables()
	{

	}

}