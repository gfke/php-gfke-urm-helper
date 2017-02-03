# php-gfke-urm-helper

utility to work with gfke urm

## Installation

```
$ composer require gfke/urm-helper
```

## Usage

**URM Rest Endpoints**

```PHP
$urm = new URM(URM_URL);
$res = $urm->query('event', [
  'key' => 'value'
]);

var_dump($res['data']);
```

**URM Socket Endpoints**

```PHP
$urmSocket = new URMSocket(
  URM_SOCKET_URL,
  URM_SOCKET_BASE_EVENT
);

$res = $urmSocket->emit('event1', [
  'key' => 'value'
]);

var_dump($res['data']);

$res = $urmSocket->emit('event2', [
  'key' => 'value'
]);

var_dump($res['data']);

$urmSocket->disconnect();
```

## Testing

```
$ ./vendor/bin/phpunit tests/ --colors
```
