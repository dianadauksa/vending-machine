<?php

function createProduct(string $name, int $price): stdClass
{
    $product = new stdClass();
    $product->name = $name;
    $product->price = $price;
    return $product;
}

function centsToEur(int $amount): float
{
    return ($amount / 100);
}

$products = [
    createProduct('Kinder Bueno', 150), // 1.5 EUR
    createProduct('Serenade', 100), // 1 EUR
    createProduct('Pringles', 260), // 2.6 EUR
    createProduct("Ben&Jerry's", 490), // 4.9 EUR
    createProduct('Cheese String', 70), // 0.7 EUR
    createProduct('KitKat', 120) // 1.2 EUR
];
// key is value and value is amount (200 cent coins (2 EUR) => 20x in machine);
$coinsInMachine = [200 => 1, 100 => 0, 50 => 0, 20 => 1, 10 => 5, 5 => 10, 2 => 50, 1 => 100];

//Interaction starts
echo "Products available today =>\n";
foreach ($products as $key => $product) {
    echo "[$key] $product->name (" . centsToEur($product->price) . " EUR)\n";
}
$selection = (int)readline('Choose product (by number) to buy: ');
$selectedProduct = $products[$selection];
if ($selectedProduct === null) {
    echo 'Invalid selection' . PHP_EOL;
    exit;
}

//money input and validation
$buyerMoney = 0;
while ($buyerMoney < $selectedProduct->price) {
    $insertedCoin = intval(readline("Insert coins (cents) >> "));
    if (!in_array($insertedCoin, array_keys($coinsInMachine))) {
        echo "Invalid coin. Here, take it back!\n";
    } else {
        $coinsInMachine[$insertedCoin]++; //increment the amount of inserted coin in coinsInMachine (e.g. if 1 EUR inserted 100=> 1 increase amount 100 => 2)
        $buyerMoney += $insertedCoin;
        echo "-------------------\n";
        echo "Inserted coins: " . centsToEur($buyerMoney) . "EUR" . PHP_EOL;
    }
}

//change calculation and issue in available coins
$change = $buyerMoney -= $selectedProduct->price;
echo "Thank you for purchasing {$selectedProduct->name}!\nHere's your change => " . centsToEur($change) . "EUR issued with: \n";
foreach (array_keys($coinsInMachine) as $coin) {
    $timesCoinInChange = intdiv($change, $coin);
    if ($timesCoinInChange > 0 && $coinsInMachine[$coin] > 0) {
        if ($coinsInMachine[$coin] >= $timesCoinInChange) {
            if ($coin >= 100) {
                echo " > ({$timesCoinInChange}x) " . centsToEur($coin) . " EUR\n";
            } else {
                echo " > ({$timesCoinInChange}x) $coin cents\n";
            }
            $coinsInMachine[$coin] -= $timesCoinInChange;
            $change -= $coin * $timesCoinInChange;
            $timesCoinInChange = 0;
        }
        if ($timesCoinInChange > 0 && $coinsInMachine[$coin] !== 0) {
            echo " > (" . $coinsInMachine[$coin] . "x) $coin cents\n";
            $change -= $coin * $coinsInMachine[$coin];
            $coinsInMachine[$coin] = 0;
        }
    }
    if ($change <= 0) {
        return;
    }
}
echo $change > 0 ? "I don't have enough coins to give you the change. :(\n" : null;