<?php

namespace twentyfouronline\Tests\Unit\Data;

use App\Facades\twentyfouronlineConfig;
use twentyfouronline\Data\Store\Kafka;
use twentyfouronline\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

#[Group('external-dependencies')]
class KafkaDBStoreTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        twentyfouronlineConfig::set('kafka.enable', true);
        twentyfouronlineConfig::set('kafka.broker.list', 'localhost:9092');
        twentyfouronlineConfig::set('kafka.topic', 'twentyfouronline');
        twentyfouronlineConfig::set('kafka.idempotence', false);
        twentyfouronlineConfig::set('kafka.buffer.max.message', 10);
        twentyfouronlineConfig::set('kafka.batch.max.message', 25);
        twentyfouronlineConfig::set('kafka.linger.ms', 5000);
        twentyfouronlineConfig::set('kafka.request.required.acks', 0);
    }

    public function testDataPushToKafka()
    {
        $producer = \Mockery::mock(Kafka::getClient());
        $producer->shouldReceive('newTopic')->once();

        /** @var \RdKafka\Producer $producer */
        $producer = $producer;
        $kafka = new Kafka($producer);

        $device = ['device_id' => 1, 'hostname' => 'testhost'];
        $measurement = 'excluded_measurement';
        $tags = ['ifName' => 'testifname', 'type' => 'testtype'];
        $fields = ['ifIn' => 234234, 'ifOut' => 53453];

        $metadata = [
            'device' => $device,
        ];
        $kafka->write($measurement, $fields, $tags, $metadata);
    }

    protected function tearDown(): void
    {
        twentyfouronlineConfig::set('kafka.enable', false);
        parent::tearDown();
    }
}




