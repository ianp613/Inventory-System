<?php
    include("app/db/db.php");
    include("app/models/models.php");
    include("app/data/data.php");
    include("app/support/support.php");
    require_once __DIR__ . '/vendor/autoload.php';

    use Predis\Client as PredisClient;

    $redis = new PredisClient([
        'scheme'   => 'tcp',
        'host'     => '127.0.0.1',
        'port'     => 6379,
        'password' => '',
        'database' => 0,
    ]);
?>