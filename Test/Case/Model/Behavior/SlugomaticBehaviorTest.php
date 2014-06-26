<?php

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');

require_once CAKE . 'Test' . DS . 'CASE' . DS . 'Model' . DS . 'models.php';

class SlugomaticBehaviorTest extends CakeTestCase {

    public $fixtures = array('plugin.slugomatic.slugomatic_post');

    public function setUp() {
        parent::setUp();

        $this->Post = ClassRegistry::init('Post');

        $this->Post->useTable = 'slugomatic_posts';
        $this->Post->Behaviors->load('Containable');
        $this->Post->Behaviors->load('Slugomatic.Slugomatic');
    }

    public function tearDown() {
        unset($this->Post);
        parent::tearDown();
    }

    public function testFixtures() {
        $data = $this->Post->find('all', array('contain' => false));
        $this->assertEqual(2, count($data));
    }

    public function testBasicSlugCreation() {
        $data = array('title' => 'My first slug');
        $savedData = $this->Post->save($data);
        $this->assertEqual('my-first-slug', $savedData['Post']['slug']);
    }

    public function testSlugDuplication() {
        $data = array('title' => 'My first post');
        $savedData = $this->Post->save($data);
        $this->assertEqual('my-first-post-1', $savedData['Post']['slug']);

        $this->Post->create();
        $savedData = $this->Post->save($data);
        $this->assertEqual('my-first-post-2', $savedData['Post']['slug']);
    }

    public function testScope() {
        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('scope' => 'deleted'));

        $data = array(
            'title' => 'Building a cakephp behavior', //There is a similar title loaded from the fixtures
            'deleted' => 0
        );

        $savedData = $this->Post->save($data);
        $this->assertEqual('building-a-cakephp-behavior', $savedData['Post']['slug']);
    }

    public function testMultipleFields() {
        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('fields' => array('code', 'title')));

        $data = array(
            'title' => 'My beautiful product',
            'code' => 'MBP001'
        );

        $savedData = $this->Post->save($data);
        $this->assertEqual('mbp001-my-beautiful-product', $savedData['Post']['slug']);
    }

    public function testAlternativeSeparator() {
        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('separator' => '|'));

        $data = array('title' => 'I\'m running out of titles');
        $savedData = $this->Post->save($data);
        $this->assertEqual('i|m|running|out|of|titles', $savedData['Post']['slug']);
    }

    public function testRecordUpdate() {
        $this->Post->recursive = -1;
        $data = $this->Post->findById(1);
        $data['Post']['title'] = 'My new title';
        $savedData = $this->Post->save($data);
        $this->assertEqual('my-first-post', $savedData['Post']['slug']);

        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('overwrite' => true));

        $data['Post']['title'] = 'Another title here';
        $savedData = $this->Post->save($data);
        $this->assertEqual('another-title-here', $savedData['Post']['slug']);
    }

    public function testLength() {
        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('length' => 7));

        $data = array('title' => 'Long title here');
        $savedData = $this->Post->save($data);
        $this->assertEquals(7, strlen($savedData['Post']['slug']));
    }

    public function testCasePreservation() {
        $this->Post->Behaviors->load('Slugomatic.Slugomatic', array('lower' => false));

        $data = array('title' => 'Oh My GoSh');
        $savedData = $this->Post->save($data);
        $this->assertEqual('Oh-My-GoSh', $savedData['Post']['slug']);
    }
}