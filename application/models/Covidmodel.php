<?php
    class Covidmodel extends CI_Model
    {
        public function insert_dati_regioni($lastdata) { // inserisce i dati delle regioni nel database covid_19
            $this->db->db_debug = FALSE;
            $sql1 = "INSERT INTO Regioni (codReg, nome, latitudine, longitudine) VALUES (?, ?, ?, ?);";
            $sql2 = "INSERT INTO Datiregioni (codReg, data, ricoverati, terapia_intensiva, isolamento_domiciliare, nuovi_att_pos, guariti, deceduti, tamponi) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";

            //$sql_trento = "UPDATE Datiregioni SET ricoverati = ricoverati + ?, terapia_intensiva = terapia_intensiva + ?, isolamento_domiciliare = isolamento_domiciliare + ?, nuovi_att_pos = nuovi_att_pos + ?, guariti = guariti + ?, deceduti = deceduti + ?, tamponi = tamponi + ? WHERE codReg = 4 AND data = ?";

            $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-regioni.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json);

            $trentino_array1 = array();
            $trentino_array2 = array();

            $index = 0;
            foreach ($obj as $ob) {
                $date = new DateTime($ob->data);

                if ($lastdata == null || $lastdata > $date) {
                    continue;
                }

                if ($index < 21) {
                    $ricoverati = $ob->ricoverati_con_sintomi;
                    $terapiaint = $ob->terapia_intensiva;
                    $isolamento = $ob->isolamento_domiciliare;
                    $vartotpos = $ob->variazione_totale_positivi;
                    $guariti = $ob->dimessi_guariti;
                    $deceduti = $ob->deceduti;
                    $tamponi = $ob->tamponi;
                }
                else {
                    $ricoverati = $ob->ricoverati_con_sintomi - $obj[$index - 21]->ricoverati_con_sintomi;
                    $terapiaint = $ob->terapia_intensiva - $obj[$index - 21]->terapia_intensiva;
                    $isolamento = $ob->isolamento_domiciliare - $obj[$index - 21]->isolamento_domiciliare;
                    $vartotpos = $ob->variazione_totale_positivi - $obj[$index - 21]->variazione_totale_positivi;
                    $guariti = $ob->dimessi_guariti - $obj[$index - 21]->dimessi_guariti;
                    $deceduti = $ob->deceduti - $obj[$index - 21]->deceduti;
                    $tamponi = $ob->tamponi - $obj[$index - 21]->tamponi;
                }

                $formattedDate = $date->format('Y-m-d');

                if ($ob->denominazione_regione == "P.A. Bolzano") {
                    $ob->denominazione_regione = "Trentino Alto Adige";
                    $trentino_array1 = array(04, $ob->denominazione_regione, $ob->lat, $ob->long);
                    $trentino_array2 = array(04, $formattedDate, $ricoverati, $terapiaint, $isolamento, $vartotpos, $guariti, $deceduti, $tamponi);
                
                    $index++;
                    continue;
                }

                if ($ob->denominazione_regione == "P.A. Trento" && $trentino_array2[1] == $formattedDate) {
                    $trentino_array2[2] += $ricoverati;
                    $trentino_array2[3] += $terapiaint;
                    $trentino_array2[4] += $isolamento;
                    $trentino_array2[5] += $vartotpos;
                    $trentino_array2[6] += $guariti;
                    $trentino_array2[7] += $deceduti;
                    $trentino_array2[8] += $tamponi;

                    $this->db->query($sql1, $trentino_array1);
                    $this->db->query($sql2, $trentino_array2);

                    $index++;
                    continue;
                }

                $this->db->query($sql1, array($ob->codice_regione, $ob->denominazione_regione, $ob->lat, $ob->long));
                $this->db->query($sql2, array($ob->codice_regione, $formattedDate, $ricoverati, $terapiaint, $isolamento, $vartotpos, $guariti, $deceduti, $tamponi));
            
                $index++;
            }
        }

        public function insert_dati_province($lastdata) { // inserisce tutti i dati delle province nel database covid_19
            $this->db->db_debug = FALSE;
            $sql1 = "INSERT INTO Province (codProv, codReg, nome, sigla, latitudine, longitudine) VALUES (?, ?, ?, ?, ?, ?);";
            $sql2 = "INSERT INTO Datiprovince (codProv, data, totale_casi) VALUES (?, ?, ?);";

            $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-province.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json);

            foreach ($obj as $k => $ob) { // Rimuove tutte le province che riportano il nome "In fase di definizione/aggiornamento"
                if ($ob->denominazione_provincia == "In fase di definizione/aggiornamento") {
                    unset($obj[$k]);
                }
            }

            $obj = array_values($obj); // Reindicizza l'array dopo aver tolto gli elementi

            $index = 0;
            foreach ($obj as $ob) {
                $date = new DateTime($ob->data);

                if ($lastdata == null || $lastdata > $date) {
                    continue;
                }

                if ($index < 107) {
                    $totcasi = $ob->totale_casi;
                }
                else {
                    $totcasi = $ob->totale_casi - $obj[$index - 107]->totale_casi;
                }

                $formattedDate = $date->format('Y-m-d');
                
                $this->db->query($sql1, array($ob->codice_provincia, $ob->codice_regione, $ob->denominazione_provincia, $ob->sigla_provincia, $ob->lat, $ob->long));
                $this->db->query($sql2, array($ob->codice_provincia, $formattedDate, $totcasi));
                
                $index++;
            }
        }

        public function update_data() {
            $this->covidmodel->insert_dati_regioni(new DateTime($this->covidmodel->get_dataora()));
            $this->covidmodel->insert_dati_province(new DateTime($this->covidmodel->get_dataora()));
        }

        public function get_regioni() {
            /* $this->load->library('curl');

            $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-regioni-latest.json');
            $json = str_replace("long", "lon", $json);
            $dati = json_decode($json); */

            $dati = $this->db->query("select r.nome as denominazione_regione, r.latitudine as lat, r.longitudine as lon, sum(ricoverati + terapia_intensiva + isolamento_domiciliare + guariti + deceduti) as totale_casi from datiregioni dr inner join regioni r on dr.codReg = r.codReg group by r.nome, r.latitudine, r.longitudine;");
            $dati = $dati->result();

            return $dati;
        }

        public function get_andamento_naz() {
            /* $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-andamento-nazionale.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json);
            $dati = end($obj); */

            $obj = $this->db->query("select sum(ricoverati) as ricoverati_con_sintomi, sum(terapia_intensiva) as terapia_intensiva, sum(isolamento_domiciliare) as isolamento_domiciliare, sum(ricoverati + terapia_intensiva + isolamento_domiciliare) as totale_positivi, sum(guariti) as dimessi_guariti, sum(deceduti) as deceduti, sum(tamponi) as tamponi, sum(ricoverati + terapia_intensiva + isolamento_domiciliare + guariti + deceduti) as totale_casi from datiregioni;");
            $obj = $obj->result();
            $dati = end($obj);

            return $dati;
        }

        public function get_storico() {
            /* $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-andamento-nazionale.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json); */

            $json = $this->db->query("select data, sum(ricoverati_con_sintomi) as ricoverati_con_sintomi, sum(terapia_intensiva) as terapia_intensiva, sum(isolamento_domiciliare) as isolamento_domiciliare, sum(ricoverati_con_sintomi + terapia_intensiva + isolamento_domiciliare) as totale_positivi, sum(dimessi_guariti) as dimessi_guariti, sum(deceduti) as deceduti, sum(tamponi) as tamponi from datiregionitot group by data;");
            $obj = $json->result();

            $dati = array(array(), array(), array());

            foreach($obj as $ob) {
                $date = new DateTime($ob->data);

                array_push($dati[0], array("label" => $date->format('d/m/Y'), "y" => $ob->totale_positivi));
                array_push($dati[1], array("label" => $date->format('d/m/Y'), "y" => $ob->dimessi_guariti));
                array_push($dati[2], array("label" => $date->format('d/m/Y'), "y" => $ob->deceduti));
            }

            return $dati;
        }

        public function get_incremento() {
            /* $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-andamento-nazionale.json');
            
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json); */

            $json = $this->db->query("select data, sum(variazione_totale_positivi) as variazione_totale_positivi from datiregionitot group by data;");
            $obj = $json->result();

            $dati = array();

            foreach($obj as $ob) {
                $date = new DateTime($ob->data);

                array_push($dati, array("label" => $date->format('d/m/Y'), "y" => $ob->variazione_totale_positivi));
            }

            return $dati;
        }

        public function get_prov() {
            /* $this->load->library('curl');

            $dati = json_decode(file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-regioni-latest.json')); */

            $json = $this->db->query("select nome as denominazione_regione from regioni order by nome asc;");
            $dati = $json->result();

            $prov = array();

            foreach($dati as $obj) {
                array_push($prov, $obj->denominazione_regione);
            }

            return $prov;
        }

        public function get_province(String $regione) {
            /* $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-province-latest.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json); */

            $json = $this->db->query("select p.nome as denominazione_provincia, r.nome as denominazione_regione, sum(totale_casi) as totale_casi from datiprovince dp inner join province p on dp.codProv = p.codProv inner join regioni r on p.codReg = r.codReg group by p.nome, r.nome;");
            $obj = $json->result();

            $dati = array();

            foreach($obj as $ob) {
                if ($ob->denominazione_regione === $regione && $ob->denominazione_provincia != "In fase di definizione/aggiornamento") {
                    array_push($dati, array("label" => $ob->denominazione_provincia, "y" => $ob->totale_casi));
                }
            }

            return $dati;
        }

        public function get_dataora() {
            /* $json = file_get_contents('https://raw.githubusercontent.com/pcm-dpc/COVID-19/master/dati-json/dpc-covid19-ita-andamento-nazionale-latest.json');
            if(substr($json, 0, 3) == pack("CCC", 0xEF, 0xBB, 0xBF)) {
                $json = substr($json, 3);
            }
            $obj = json_decode($json); */

            $json = $this->db->query("select max(data) as data from datiregioni;");
            $obj = $json->result();

            $latest = end($obj);

            return $latest->data;
        }
    }
?>