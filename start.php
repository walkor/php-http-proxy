<?php 
/**
 * This file is part of php-http-proxy.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */
use \Workerman\Worker;
use \Workerman\Connection\AsyncTcpConnection;

// Autoload.
require_once __DIR__ . '/vendor/autoload.php';

// Create a TCP worker.
$worker = new Worker('tcp://0.0.0.0:8080');
// 6 processes
$worker->count = 6;
// Worker name.
$worker->name = 'php-http-proxy';

// Emitted when data received from client.
$worker->onMessage = function($connection, $buffer)
{
    // Parse http header.
    list($method, $addr, $http_version) = explode(' ', $buffer);
    $url_data = parse_url($addr);
    $addr = !isset($url_data['port']) ? "{$url_data['host']}:80" : "{$url_data['host']}:{$url_data['port']}";
    // Async TCP connection.
    $remote_connection = new AsyncTcpConnection("tcp://$addr");
    // CONNECT.
    if ($method !== 'CONNECT') {
        $remote_connection->send($buffer);
    // POST GET PUT DELETE etc.
    } else {
        $connection->send("HTTP/1.1 200 Connection Established\r\n\r\n");
    }
    // Pipe.
    $remote_connection ->pipe($connection);
    $connection->pipe($remote_connection);
    $remote_connection->connect();
};

// Run.
Worker::runAll();
