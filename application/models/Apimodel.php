<?php 

class Apimodel extends CI_Model {
    public function get_andamento() {
        $results = $this->db->query("select data, sum(ricoverati_con_sintomi) as ricoverati_con_sintomi, sum(terapia_intensiva) as terapia_intensiva, sum(isolamento_domiciliare) as isolamento_domiciliare, sum(ricoverati_con_sintomi + terapia_intensiva + isolamento_domiciliare) as totale_positivi, sum(variazione_totale_positivi) as variazione_totale_positivi, sum(dimessi_guariti) as dimessi_guariti, sum(deceduti) as deceduti, sum(tamponi) as tamponi from datiregionitot group by data;");
        $obj = $results->result();

        return $obj;
    }

    public function get_regioni() {
        $results = $this->db->query("select data, codice_regione, r.nome as denominazione_regione, r.latitudine as lat, r.longitudine as lon, ricoverati_con_sintomi, terapia_intensiva, isolamento_domiciliare, (ricoverati_con_sintomi + terapia_intensiva + isolamento_domiciliare) as totale_positivi, dimessi_guariti, deceduti, tamponi from datiregionitot drt inner join regioni r on drt.codice_regione = r.codReg order by codice_regione asc, data asc;");
        $obj = $results->result();

        return $obj;
    }

    public function get_regioni_latest() {
        $results = $this->db->query("select data, codice_regione, r.nome as denominazione_regione, r.latitudine as lat, r.longitudine as lon, ricoverati_con_sintomi, terapia_intensiva, isolamento_domiciliare, (ricoverati_con_sintomi + terapia_intensiva + isolamento_domiciliare) as totale_positivi, dimessi_guariti, deceduti, tamponi from datiregionitot drt inner join regioni r on drt.codice_regione = r.codReg where data = (select MAX(data) from datiregionitot);");
        $obj = $results->result();

        return $obj;
    }

    public function get_province() {
        $results = $this->db->query("select data, codice_provincia, p.nome as denominazione_provincia, r.codReg as codice_regione, r.nome as denominazione_regione, p.latitudine as lat, p.longitudine as lon, totale_casi from datiprovincetot dpt inner join province p on codice_provincia = p.codProv inner join regioni r on p.codReg = r.codReg order by data asc, codice_provincia asc, codice_regione asc");
        $obj = $results->result();

        return $obj;
    }

    public function get_province_latest() {
        $results = $this->db->query("select data, codice_provincia, denominazione_provincia, r.codReg as codice_regione, r.nome as denominazione_regione, p.latitudine as lat, p.longitudine as lon, totale_casi from totalecasiprovince tp inner join province p on codice_provincia = p.codProv inner join regioni r on p.codReg = r.codReg");
        $obj = $results->result();

        return $obj;
    }
}

?>