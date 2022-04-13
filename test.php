<?php

use Stripe\StripeClient;

require __DIR__ . '/vendor/autoload.php';

$client = new StripeClient('sk_test_51KnKXQCzFoffofca0S19lw5tRiM83cGEi6Q2meQPVgAqokuJ3OjnLydCVZBCiu2U5zkXUR7FMrbZbZei0qwI7b6E00lts3DPQ1');
$collection = $client->customers->search([
	'query' => "metadata['account']: '1'",
]);

var_dump($collection->count());
var_dump($collection->data);
