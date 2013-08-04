# Object-oriented configuration for PHP

YetAnother Config — простой менеджер конфигурационных файлов.

## Установка

Рекомендуемая установка через [composer](http://getcomposer.org):
```JSON
{
    "require": {
        "yetanother/config": "dev-master"
    }
}
```

## Одиночный конфигурационный файл
```php
// path/to/config.php

return array(
    'parameter1' => 'value1',
    'parameter2' => 'value2'
);
```
```php
use YetAnother\Config\Config;

$config = new Config('path/to/config.php');
echo $config['parameter1']; // value1
```

## Директория с конфигурационными файлами
```
config
┕database.php
┕routing.php
┕security.php
```
```php
use YetAnother\Config\Config;

$config = new Config('path/to/config');
echo $config['database']['host'];
```

## Использование переменных вместо массива
```php
// path/to/config.php

$parameter1 = 'value1';
$parameter2 = 'value2';
```
```php
use YetAnother\Config\Config;

$config = new Config('path/to/config.php');
echo $config['parameter1'];
```

## Включение других конфигурационных файлов
```
config
┕config.php
┕database.php
┕routing.php
┕security.php
```
```php
// config.php

$database = include(__DIR__.'/database.php');
$routing = include(__DIR__.'/routing.php');
$security = include(__DIR__.'/security.php');
// или
$database = $this->import('database');
$routing = $this->import('routing');
$security = $this->import('security');
// или
$database = $this->import(__DIR__.'/database.php');
$routing = $this->import(__DIR__.'/routing.php');
$security = $this->import(__DIR__.'/security.php');
```
```php
use YetAnother\Config\Config;

$config = new Config('config/config.php');
echo $config['database']['host'];
```