<?php

class Json {

    protected $CI;

    public function __construct() {
        $this->CI =& get_instance();
    }

    public function write($data) {
        $this->CI->output->set_content_type('application/json');
        $this->CI->output->set_output(json_encode($data));
    }

    public function write_for_chart($data) {
        $this->CI->output->set_content_type('application/json');
        $this->CI->output->set_output(json_encode($data, JSON_NUMERIC_CHECK));
    }

}

?>