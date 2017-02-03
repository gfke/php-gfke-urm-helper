<?php

use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;
use GuzzleHttp\Promise\Promise;

class URMSocket
{
  private $url = '';
  private $baseEvent = '';
  private $client = null;

  /**
   * URMSocket constructor.
   * @param string $url
   * @param string $baseEvent
   */
  public function __construct($url, $baseEvent)
  {
    $this->url = $url;
    $this->baseEvent = $baseEvent;
    $this->connect();
  }

  private function connect()
  {
    $this->client = new Client(new Version1X($this->url));
    $this->client->initialize();
  }

  public function disconnect()
  {
    $this->client->close();
    $this->client = null;
  }

  public function emit($event, $data = array())
  {
    $emitEvent = $this->baseEvent . $event;

    if ($this->client == null) {
      $this->connect();
    }

    $client = $this->client;
    $promise = new Promise(function () use (&$promise, $client) {
      $r = null;
      $waiting = true;
      while ($waiting) {
        $r = $client->read();
        if (!empty($r)) {
          $waiting = false;
        }
      }
      $promise->resolve($r);
    });

    $this->client->emit($emitEvent, $data);

    // Calling wait will return the value of the promise.
    $res = $promise->wait();
    $res = substr($res, 2);
    $res = json_decode($res);
    return [
      'event' => $res[0],
      'data' => $res[1]
    ];
  }
}
