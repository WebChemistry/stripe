<?php

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

require __DIR__ . '/vendor/autoload.php';

$client = new StripeClient('sk_test_51KnKXQCzFoffofca0S19lw5tRiM83cGEi6Q2meQPVgAqokuJ3OjnLydCVZBCiu2U5zkXUR7FMrbZbZei0qwI7b6E00lts3DPQ1');
try {
	$collection = $client->customers->retrieve('cus_asdasdsa');
} catch (ApiErrorException $exception) {
	var_dump($exception);
}

