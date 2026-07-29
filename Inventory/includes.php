<?php
    include("app/db/db.php");
    include("app/models/models.php");
    include("app/data/data.php");
    include("app/support/support.php");
    require_once __DIR__ . '/vendor/autoload.php';

    use Predis\Client as PredisClient;
    use Predis\Connection\ConnectionException;

    try {
        $redis = new PredisClient([
            'scheme'   => 'tcp',
            'host'     => '127.0.0.1',
            'port'     => 6379,
            'password' => '',
            'database' => 0,
        ]);

        // Force a connection to verify Redis is reachable.
        $redis->connect();

        // Alternatively:
        // $redis->ping();

    } catch (ConnectionException $e) {
        die("Redis Connection Error: " . $e->getMessage());
    } catch (\Predis\Response\ServerException $e) {
        die("Redis Server Error: " . $e->getMessage());
    } catch (\Exception $e) {
        die("General Error: " . $e->getMessage());
    }
?>