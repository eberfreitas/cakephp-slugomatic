<?php

App::uses('CakeTestFixture', 'TestSuite/Fixture');

class SlugomaticPostFixture extends CakeTestFixture {

	public $fields = array(
		'id' => array('type' => 'integer', 'key' => 'primary'),
		'title' => array('type' => 'string', 'null' => true),
		'slug' => array('type' => 'string', 'null' => true),
		'code' => array('type' => 'string', 'null' => true),
		'deleted' => array('type' => 'integer', 'null' => false, 'default' => 0)
	);

	public $records = array(
		array('title' => 'My first post', 'slug' => 'my-first-post', 'code' => null, 'deleted' => 0),
		array('title' => 'Building a cakephp behavior', 'slug' => 'building-a-cakephp-behavior', 'code' => null, 'deleted' => 1)
	);
}