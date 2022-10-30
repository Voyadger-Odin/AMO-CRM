<?php

$cars = [
    ['name' => 'Такси 1', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 2', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 3', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 4', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
    ['name' => 'Такси 5', 'position' => rand(0, 1000), 'isFree' => (bool) rand(0, 1)],
];

$passenger = rand(0, 1000);

/* ===== Ваш код ниже ===== */

// Поиск
$carIdFind = -1;
for ($carId = 0; $carId < count($cars); $carId++){
    // Отсеивает занятые
    if (!$cars[$carId]['isFree']){
        continue;
    }
    // Если такси не выбрано, то берёт первое свободное
    if ($carIdFind < 0){
        $carIdFind = $carId;
        continue;
    }
    // Если выбрано, то проверяет остальные, если они окажутся ближе
    if (abs($cars[$carId]['position'] - $passenger) < abs($cars[$carIdFind]['position'] - $passenger)){
        $carIdFind = $carId;
    }
}

// Печать
foreach ($cars as $carId => $car){
    echo $car['name']
        . ', стоит на '
        . $car['position']
        . ' км, до пассажира '
        . abs($car['position'] - $passenger)
        . ' км ('
        . ($car['isFree'] ? 'свободен' : 'занят')
        . ')';
    if ($carId === $carIdFind){
        echo ' - едет это такси';
    }

    echo '<br>';
}
