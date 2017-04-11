# WP-Donors-Choose-Interface
A simple Wordpress plugin with methods to retrieve data from DonorsChoose.org via their API

## Requirements
**Wordpress** - currently tested on version 4.7.3
**php-curl** - curl is required to interact with the DonorsChoose API

## Installation
Upload zipped plugin files to Wordpress and activate the plugin.

## Use
Provide DonorsChoose API Key
You can get an API key by contacting DonorsChoose.org. For testing purposes, don't set the $api_key, or change it to 'DONORSCHOOSE'.

### Initiate the connection and use the methods:
```php
$api_key = 'examplekey'; 
$DC = new DCInterface($api_key);


$list = $DC->getList();
```

## Methods
* getList($query)
* getSingle($id)
* projectLink($id)
* fundLink($id,$amount)
* prettySynopsis($synopsis)
* getSubject($ref)
* getGrade($ref)
