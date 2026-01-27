<?php

function calculateBillAmount($type, $units)
{
    if ($type == "HOUSEHOLD") {
        $minCharge = 25;
        if ($units <= 50)
            $amt = $units * 1.5;
        elseif ($units <= 100)
            $amt = (50 * 1.5) + (($units - 50) * 2.5);
        elseif ($units <= 150)
            $amt = (50 * 1.5) + (100 * 2.5) + (($units - 100) * 3.5);
        else
            $amt = (50 * 1.5) + (100 * 2.5) + (50 * 3.5) + (($units - 150) * 4.5);
    } elseif ($type == "COMMERCIAL") {
        $minCharge = 50;
        if ($units <= 50)
            $amt = $units * 2.5;
        elseif ($units <= 100)
            $amt = (50 * 2.5) + (($units - 50) * 3.5);
        elseif ($units <= 150)
            $amt = (50 * 2.5) + (100 * 3.5) + (($units - 100) * 4.5);
        else
            $amt = (50 * 2.5) + (100 * 3.5) + (50 * 4.5) + (($units - 150) * 5.5);
    } else { // INDUSTRY
        $minCharge = 100;
        if ($units <= 50)
            $amt = $units * 3.5;
        elseif ($units <= 100)
            $amt = (50 * 3.5) + (($units - 50) * 4.5);
        elseif ($units <= 150)
            $amt = (50 * 3.5) + (100 * 4.5) + (($units - 100) * 5.5);
        else
            $amt = (50 * 3.5) + (100 * 4.5) + (50 * 5.5) + (($units - 150) * 6.5);
    }

    return max($amt, $minCharge);
}

$tests = [
    ['type' => 'HOUSEHOLD', 'units' => 50, 'expected' => 75.00],
    ['type' => 'HOUSEHOLD', 'units' => 120, 'expected' => 395.00],
    ['type' => 'COMMERCIAL', 'units' => 60, 'expected' => 160.00],
    ['type' => 'INDUSTRY', 'units' => 5, 'expected' => 100.00],
];

echo "Running Calculation Tests...\n\n";

foreach ($tests as $t) {
    $result = calculateBillAmount($t['type'], $t['units']);
    $status = ($result == $t['expected']) ? "PASS" : "FAIL";
    echo sprintf("[%s] %s (%d u): Got %.2f, Expected %.2f\n", $status, $t['type'], $t['units'], $result, $t['expected']);
}
?>