<?php

class Helper
{
  /**
   * Combine two urls. (https://gist.github.com/amnuts/7598576)
   *
   * The urls can be either a string or url parts that consist of:
   *
   *     scheme, host, port, user, pass, path, query, fragment
   *
   * If passed in as parts in an array, the query parameter can be either
   * a string or an array of name/value key pairs.  The query parameters
   * will be added on to the ones from the original url.  If you want to
   * remove query parameters, or any other parts of the url, you need to
   * pass the value in as null.
   *
   * Examples:
   *
   *     urlMerge(
   *         '/tests/section/people?id=9405',
   *         array('query' => array('found' => true, 'id' => null))
   *     );
   *
   *     urlMerge(
   *         'http://www.example.com/',
   *         array('scheme' => 'https', 'query' => 'foo=bar&test=1'
   *     );
   *
   *     urlMerge(
   *         array('path' => '/tests/item', 'query' => 'id=9405'),
   *         'http://www.example.com'
   *     );
   *
   * @param  string|array $original
   * @param  string|array $new
   * @return string
   */
  public static function urlMerge($original, $new)
  {
    if (is_string($original)) {
      $original = parse_url($original);
    }
    if (is_string($new)) {
      $new = parse_url($new);
    }
    $qs = null;
    if (!empty($original['query']) && is_string($original['query'])) {
      parse_str($original['query'], $original['query']);
    }
    if (!empty($new['query']) && is_string($new['query'])) {
      parse_str($new['query'], $new['query']);
    }
    if (isset($original['query']) || isset($new['query'])) {
      if (!isset($original['query'])) {
        $qs = $new['query'];
      } elseif (!isset($new['query'])) {
        $qs = $original['query'];
      } else {
        $qs = array_merge($original['query'], $new['query']);
      }
    }
    $result = array_merge($original, $new);
    $result['query'] = $qs;
    foreach ($result as $k => $v) {
      if ($v === null) {
        unset($result[$k]);
      }
    }
    if (!empty($result['query'])) {
      $result['query'] = http_build_query($result['query']);
    }
    if ($result['path'][0] != '/') {
      $result['path'] = "/{$result['path']}";
    }
    return (isset($result['scheme']) ? "{$result['scheme']}://" : '')
      . (isset($result['user']) ? $result['user']
        . (isset($result['pass']) ? ":{$result['pass']}" : '') . '@' : '')
      . (isset($result['host']) ? $result['host'] : '')
      . (isset($result['port']) ? ":{$result['port']}" : '')
      . (isset($result['path']) ? $result['path'] : '')
      . (!empty($result['query']) ? "?{$result['query']}" : '')
      . (isset($result['fragment']) ? "#{$result['fragment']}" : '');
  }
}
