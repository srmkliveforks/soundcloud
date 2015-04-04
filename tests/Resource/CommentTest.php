<?php

namespace Njasm\Soundcloud\Tests\Resource;

use \Njasm\Soundcloud\Resource\Comment;
use \Njasm\Soundcloud\Soundcloud;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    use \Njasm\Soundcloud\Tests\MocksTrait, \Njasm\Soundcloud\Tests\ReflectionsTrait;

    public $sc;
    public $comment;
    public $commentSerialized;
    public $commentUnserialized;

    public function setUp()
    {
        $this->sc = new Soundcloud("ClientID", "ClientSecret");
        $this->commentUnserialized = ['body' => 'Test Comment', 'track_id' => 12345, 'user_id' => 6789, 'timestamp' => 123456789];
        $this->commentSerialized = ['comment[body]' => 'Test Comment', 'comment[track_id]' => 12345, 'comment[user_id]' => 6789, 'comment[timestamp]' => 123456789];
        $this->comment = new Comment($this->sc, $this->commentUnserialized);
    }

    public function testSerialize()
    {
        $returnValue = $this->comment->serialize();
        $this->assertEquals($this->commentSerialized, $returnValue);
    }

    public function testSerializeWithNull()
    {
        $commentUnserialized = ['body' => 'Test Comment', 'track_id' => 12345, 'user_id' => 6789];
        $commentSerialized = ['comment[body]' => 'Test Comment', 'comment[track_id]' => 12345, 'comment[user_id]' => 6789, 'comment[timestamp]' => null];
        $comment = new Comment($this->sc, $commentUnserialized);
        $returnValue = $comment->serialize();
        $this->assertEquals($returnValue, $commentSerialized);
    }

    public function testSerializeException()
    {
        $property = new \ReflectionProperty($this->comment, "writableProperties");
        $property->setAccessible(true);
        $property->setValue($this->comment, []);

        $this->setExpectedException('\Exception');
        $this->comment->serialize();
    }

    public function testIsNew()
    {
        $this->assertTrue($this->comment->isNew());
        $this->comment->set("id", 1);
        $this->assertFalse($this->comment->isNew());
        $this->assertEquals(1, $this->comment->id());
    }

    public function testSave()
    {
        $data = include __DIR__ . '/../Data/Serialized_Comment.php';
        $response = $this->getResponseMock('bodyRaw', function() use ($data) { return $data; });
        $request = $this->getRequestMock($response);
        $factory = $this->getFactoryMock($request, $response);
        $reflectedFactory = $this->reflectProperty($this->sc, 'factory');
        $reflectedFactory->setValue($this->sc, $factory);

        $soundcloud = $this->getSoundcloudMock();
        $reflectedSoundcloud = $this->reflectProperty($this->sc, 'self');
        $reflectedSoundcloud->setValue($this->sc, $soundcloud);

        $expected = '\Njasm\Soundcloud\Collection';

        $this->comment->save();
        $this->assertEquals('225628819', $this->comment->get('id'));
    }

    public function testExistentSave()
    {
        $this->comment->set('id', 1);
        // soundcloud do not allow to delete or update a comment?
        $this->setExpectedException('\LogicException');
        $this->comment->save();
    }

    public function testRefresh()
    {
        $data = include __DIR__ . '/../Data/Serialized_Comment.php';
        $response = $this->getResponseMock('bodyRaw', function() use ($data) { return $data; });
        $request = $this->getRequestMock($response);
        $factory = $this->getFactoryMock($request, $response);
        $reflectedFactory = $this->reflectProperty($this->sc, 'factory');
        $reflectedFactory->setValue($this->sc, $factory);

        $soundcloud = $this->getSoundcloudMock();
        $reflectedSoundcloud = $this->reflectProperty($this->sc, 'self');
        $reflectedSoundcloud->setValue($this->sc, $soundcloud);

        $this->comment->save();
        $this->assertEquals('225628819', $this->comment->get('id'));
    }

    public function testDelete()
    {
        $this->setExpectedException('\Exception');
        $this->comment->delete();
    }

    public function testUpdate()
    {
        $this->setExpectedException('\Exception');
        $this->comment->update();
    }
}
