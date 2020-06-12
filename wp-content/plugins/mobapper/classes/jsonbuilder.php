<?php

class MOBAPPER_JSON_BUILDER {
  
    function __construct() {
        $this->name = "JSONManager";
        $this->jsonstr = NULL;
        $this->object = NULL;
    }

    public function createjsonObject() {
        $this->object = NULL;
        $this->object = array();
    }

    public function adddata($key, $val) {
        $this->object[$key] = $val;
    }

    public function toJson() {
        return json_encode($this->object);
    }

    static public function decodejson($data) {
        $dec = json_decode($data);
        return $dec;
    }
    
    static public function endFlow($output)
    {
        if (!headers_sent()) {
            header('HTTP/1.1 200 OK', true);
            header("Content-Type: application/json; charset=UTF-8", true);
        }
        die($output);
    }

  
}

?>
