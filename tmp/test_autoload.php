<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Utils\ViewHelper;

if (class_exists(ViewHelper::class)) {
    echo "SUCCESS: ViewHelper found.\n";
} else {
    echo "FAILURE: ViewHelper NOT found.\n";
    // Check if folder exists with correct casing
    if (is_dir(__DIR__ . '/../src/Utils')) {
        echo "src/Utils exists.\n";
    } else {
        echo "src/Utils does NOT exist.\n";
    }
    if (is_file(__DIR__ . '/../src/Utils/ViewHelper.php')) {
        echo "src/Utils/ViewHelper.php exists.\n";
    } else {
        echo "src/Utils/ViewHelper.php does NOT exist.\n";
    }
}
