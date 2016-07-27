<?php
namespace ark\tool;
/**
 * Provides a simple basic rcon client.
 * Usage:-
 * Creates an rcon object and opens a scoket to the server.
 * $rcon = new rcon('207.0.0.1', 27015, 'rconPassword');
 * $rcon->send('server command', [rconCommand (2 or 3)], [packet id]);
 *
 * For PHP the core functions are constructPayload and deliverPayload.
 *
 * Note:- There is a 4096 max bite packet capacity which is not catered for in this code.
 *        While this class works well for commands which do not expect large response, it
 *        would need modifying for large log downloads or similar.
 */
class rcon {
  private $id = 500; // Auth id and incremented if no further id is provied.
  private $socket;
  private $payload; // Encoded server command.
  private $response = []; // What came back from the server.
  private $rconCommand = []; // Holds the rcon commands list.

  public function __construct ($host, $port, $password)
  {
    $this->rconCommand = ['auth' => 3, 'scom' => 2];
    $timeout = 3;
    $socket = fsockopen($host, $port, $errNo, $errMsg, $timeout) or die('Error: '. $errNo . ' ' . $errMsg);
    if (!$socket) die('Error: '. $errNo . ' ' . $errMsg);
    $this->socket = $socket;
    // Authenticate user.
    $this->send($password, $this->rconCommand['auth'], $this->id);
    if ($this->response['ID'] != 500 && $this->response['Response'] != 2) {
      echo 'Authentication error: ', print_r($this->response);
    }
  }

  /**
   * Sends a command to the server.
   * @param  String $instruction A server command (ie: listPlayers)
   * @param  Int $rconCommand An rcon command (ie: 2)
   * @param  Int $id          Packet id
   * @return Array            Whatever came back from the server.
   */
  public function send ($instruction, $rconCommand = null, $id = null)
  {
    if ($rconCommand === null) $rconCommand = $this->rconCommand['scom'];
    if ($id === null) $id = ($this->id + 1);
    $this->constructPayload($id, $rconCommand, $instruction);
    $response = $this->deliverPayload();

    if (!is_array($response)) {
      echo 'Unknown Error: ', print_r($response, true);
      return false;
    }
    $new = ['id' => $response['ID'], 'msg' => $response['S1'], 'info' => $response['Response']];
    return $new;
  }


  /**
   * Constructs an rcon payload.
   * @param  int $id          A payload id number (any you like)
   * @param  int $rconCommand     An rcon command to send to the server (2 - authenticate, 3 - send command.).
   * @param  string $instruction A command to the server.
   * @return [type]              [description]
   */
  private function constructPayload ($id, $rconCommand, $instruction)
  {
    // Construct the payload.
    $data = pack("VV",$id,$rconCommand).$instruction.chr(0).''.chr(0);
    // Prefix payload with packet size
    $data = pack("V",strlen($data)).$data;
    $this->payload = $data;
  }

  /**
   * Writes the payload to the socket and recieves the response.
   * @return [type] [description]
   */
  function deliverPayload ()
  {
    // Send the payload to the server.
    fwrite($this->socket,$this->payload);
    // Read the server reply (first 4 bits which contain the size)
    $psize = fread($this->socket, 4);
    // Get the size from the packet.
    $size = unpack('V1Size',$psize);
    // max 4096
    $payload = fread($this->socket, $size['Size']);
    $impact = unpack("V1ID/V1Response/a*S1/a*S2",$payload);
    // Keep the response for further use (ie: debug, error message, loging)
    $this->response = $impact;
    return $impact;
  }

}
?>
