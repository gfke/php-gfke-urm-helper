<?php

use GuzzleHttp\Client;

class URM
{

  private $url = '';

  /**
   * URM constructor.
   * @param {String} $url
   */
  public function __construct($url)
  {
    $this->url = $url;
  }

  /**
   * Execute URM Query
   * @param {string|array} $route
   * @param array $data
   * @return array
   */
  public function query($route, $data = array())
  {
    if (is_string($route)) {
      $url = $this->url . $route;
    } else {
      $url = Helper::urlMerge($this->url, $route);
    }

    $client = new Client();
    $res = $client->request('POST', $url, ['json' => $data]);
    return [
      'data' => json_decode($res->getBody()),
      'response' => $res
    ];
  }
}
