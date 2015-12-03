<?php namespace Hocza\Tests\Sendy;

use Hocza\Sendy\Sendy;
use PHPUnit_Framework_TestCase;

class SendyTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleSubscribe() {
        $config = [
            'listId' => '',
            'installationUrl' => '',
            'apiKey' => '',
        ];

        $subscriber = new Sendy($config);

        $subscriber = $subscriber->subscribe([
            'nome' => 'Alison',
            'email' => 'alisonmonteiro.10@gmail.com',
        ]);

        $this->assertEquals('Subscribed.', $subscriber);
    }

    public function testSubscribeASubscriberThatAlreadyExists() {}

    public function testCheckStatus() {}

    public function testTransferSubscriberForAnotherList() {}

    public function testSimpleUnsubscribe() {}

    public function testUnsubscribeASubscriberThatNotExists() {}

    public function testGetAccount() {}
}