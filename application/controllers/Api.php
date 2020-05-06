<?php 

class Api extends CI_Controller {
    public function andamento() {
        $this->load->library('json');
        $res = $this->apimodel->get_andamento();

        $this->json->write($res);
    }

    public function regioni() {
        $this->load->library('json');
        $res = $this->apimodel->get_regioni();

        $this->json->write($res);
    }

    public function regionilatest() {
        $this->load->library('json');
        $res = $this->apimodel->get_regioni_latest();

        $this->json->write($res);
    }

    public function province() {
        $this->load->library('json');
        $res = $this->apimodel->get_province();

        $this->json->write($res);
    }

    public function provincelatest() {
        $this->load->library('json');
        $res = $this->apimodel->get_province_latest();

        $this->json->write($res);
    }
}

?>