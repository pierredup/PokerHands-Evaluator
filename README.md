## Poker Hand Evaluator

### Introduction

This is a simple poker hand evaluator, which takes a list of 5 cards and returns the rank of the hand.

The cards are represented as a string using a short-notation to represent a card,
where the first character is the rank and the second character is the suit.
Example: `2H` is the 2 of hearts, `9C` is the 9 of clubs, `KD` is the king of diamonds etc.

This short notation is used in both the CLI and the API.
The CLI will ask to select 5 cards when values aren't provided as arguments.

### Requirements

- PHP 8.2
- [Composer](https://getcomposer.org/)

### Setting Up

Clone the repository, then install all the dependencies using composer:

```bash
composer install
```

### API

The API is a very simple implementation, just using [FastRoute](https://github.com/nikic/FastRoute)
for routing and some custom HTTP handling.
Usually I would go with [Api Platform](https://api-platform.com), but it seemed a bit overkill to set up a full
Symfony framework configuration just for this single use case.

#### Accessing the API

Start a webserver pointing to `public/index.php` as the front controller.

E.G using the built-in PHP webserver:

```bash
php -S 127.0.0.1:8000 public/index.php
```

Then send a POST request to `http://127.0.0.1:8000/api` with the following body format: `{"suites": ['array of cards']}`

E.G

```bash
curl -X POST -d '{"suites": ["2H", "3D", "5S", "9C", "KD"]}' http://127.0.0.1:8000/api
```

This will return a JSON response with a `rank` key which contains the rank of the hand.

E.G

```json
{
  "rank": "High Card"
}
```

### CLI

The CLI script takes a list of 5 cards as arguments, or asks for them if none are provided.
It then prints out the selected cards and the rank of the hand.

The CLI script can be executed using the following command:

```bash
php bin/console
```

To evaluate a hand, pass the list of cards as a short notation string to the console:

```bash
php bin/console 2H 3D 5S 9C KD
```

This will output the following information:

```text
You chose the following cards:
==============================

 * 2 of Hearts
 * 3 of Diamonds
 * 5 of Spades
 * 9 of Clubs
 * King of Diamonds

The highest hand you have is:
=============================

 High Card
```

### Tests

The tests are written using PHPUnit and can be executed using the following command:

```bash
vendor/bin/phpunit
```
