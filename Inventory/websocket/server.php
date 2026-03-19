<?php
    require __DIR__ . "/../includes.php";

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;

    class UpdateServer implements MessageComponentInterface {

        protected $clients;

        public function __construct(){
            $this->clients = new \SplObjectStorage;
            echo "WebSocket running...\n";
        }

        public function onOpen(ConnectionInterface $conn){
            $this->clients->attach($conn);
        }

        public function onMessage(ConnectionInterface $from, $msg){
            echo "Update Received.";

            $ws_promise = new WebSocket_Promise;
            $client_message = explode(",",$msg);
            $send = false;
            $ws_data = null;

            if($client_message[0] == "get"){
                $send = true;
                $ws_data = DB::all($ws_promise);
            }
            if($client_message[0] == "post"){
                
            }
            if($client_message[0] == "delete"){
                
            }

            if($send){
                // broadcast to all clients
                foreach($this->clients as $client){
                    $client->send(json_encode($ws_data));
                }    
            }
        }

        public function onClose(ConnectionInterface $conn){
            $this->clients->detach($conn);
        }

        public function onError(ConnectionInterface $conn, \Exception $e){
            $conn->close();
        }

    }

    $server = Ratchet\Server\IoServer::factory(
        new Ratchet\Http\HttpServer(
            new Ratchet\WebSocket\WsServer(
                new UpdateServer()
            )
        ),
        8080
    );

    $server->run();
?>