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

  private function parseRead($read)
  {
    $read = substr($read, 2);
    return json_decode($read);
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

    $that = $this;
    $promise = new Promise(function () use (&$promise, $that) {
      while (true) {
        $r = $that->client->read();
        if (!empty($r)) {
          $r = $that->parseRead($r);
          break;
        }
      }

      $promise->resolve([
        'event' => $r[0],
        'data' => $r[1]
      ]);
    });

    $this->client->emit($emitEvent, $data);

    // Calling wait will return the value of the promise.
    return $promise->wait();
  }
}
