<?php namespace Hocza\Tests\Sendy;

use Hocza\Sendy\Sendy;
use PHPUnit_Framework_TestCase;

class SendyTest extends PHPUnit_Framework_TestCase
{
    private $config = [
        'listId' => 'YOUR_LIST_ID',
        'installationUrl' => 'YOUR_URL',
        'apiKey' => 'API_KEY_HERE',
    ];

    public function testSimpleSubscribe()
    {
        $subscriber = new Sendy($this->config);

        $subscriber = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison@gmail.com',
        ]);

        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Subscribed.', $subscriber['message']);
    }

    public function testSubscribeASubscriberThatAlreadyExists()
    {
        $subscriber = new Sendy($this->config);

        $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $subscriber = $subscriber->subscribe([
            'name' => 'Alison',
            'email' => 'alison2@gmail.com',
        ]);

        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Already subscribed.', $subscriber['message']);
    }

    public function testSimpleUnsubscribe()
    {
        $subscriber = new Sendy($this->config);

        $subscriber = $subscriber->unsubscribe('alison2@gmail.com');
        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Unsubscribed', $subscriber['message']);
    }

    public function testUnsubscribeASubscriberThatNotExists()
    {
        $subscriber = new Sendy($this->config);

        $subscriber = $subscriber->unsubscribe('zzzz@gmail.com');

        // The API doesn't provide this type of error
        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Unsubscribed', $subscriber['message']);
    }

    public function testCheckStatus()
    {
        $subscriber = new Sendy($this->config);

        $subscriber1 = $subscriber->status('zzzz@gmail.com');
        $this->assertEquals('Email does not exist in list', $subscriber1);

        $subscriber2 = $subscriber->status('alison2@gmail.com');
        $this->assertEquals('Unsubscribed', $subscriber2);

        $subscriber3 = $subscriber->status('alison@gmail.com');
        $this->assertEquals('Subscribed', $subscriber3);
    }

    public function testUpdate()
    {
        $subscriber = new Sendy($this->config);
        $subscriber = $subscriber->update('alison@gmail.com', [
            'name' => 'Alison 2',
        ]);

        // This method use `subscribe` method to update data
        $this->assertEquals(true, $subscriber['status']);
        $this->assertEquals('Already subscribed.', $subscriber['message']);
    }

    public function testDelete()
    {
        $subscriber = new Sendy($this->config);
        $subscriber->subscribe([
            'name' => 'Mark',
            'email' => 'mark@gmail.com',
        ]);
        $currentStatus = $subscriber->status('mark@gmail.com');
        $this->assertEquals('Subscribed', $currentStatus);

        $deleteResult = $subscriber->delete('mark@gmail.com');
        $this->assertEquals(true, $deleteResult['status']);

        $currentStatus = $subscriber->status('mark@gmail.com');
        $this->assertEquals('Email does not exist in list', $currentStatus);

    }
}