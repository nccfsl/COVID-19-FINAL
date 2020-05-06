<html>
    <head>
        <title>Dati COVID-19 - Italia - Andamento</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
        <link rel="icon" href="<?=base_url()?>/favicon.ico" type="image/icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/siimple@3.3.1/dist/siimple.min.css">
        <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    </head>
    <body>
        <script>
            $(document).ready(function() {
                $("#conferma").click(function() {
                    $.post("province_input", 
                    {
                        regione: $("#regione").val()
                    }, 
                    function(data, status) {
                        if (status != "success") {
                            alert("C'è stato un errore nell'invio della richiesta, riprova più tardi");
                        }
                        else {
                            chart(data);
                        }
                    });
                });
            });

            function chart(data) {
                var chart = new CanvasJS.Chart("positivi", {
                    animationEnabled: true,
                    theme: "light2",
                    axisY: {
                        title: "Casi totali",
                        includeZero: true
                    },
                    legend:{
                        cursor: "pointer",
                        verticalAlign: "center",
                        horizontalAlign: "right",
                        itemclick: toggleDataSeries
                    },
                    data: [{
                        type: "column",
                        indexLabel: "{y}",
                        dataPoints: data
                    }]
                });
                chart.render();

                function toggleDataSeries(e){
                    if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
                        e.dataSeries.visible = false;
                    }
                    else{
                        e.dataSeries.visible = true;
                    }
                    chart.render();
                }
            }
        </script>
        <navbar>
            <div class="siimple-navbar siimple-navbar--large siimple-navbar--light animated slideInDown">
                <a href="<?php echo site_url('defcont/index'); ?>" class="siimple-navbar-title">Dati COVID-19</a>
                <div class="siimple-navbar-subtitle">Dati italiani sul COVID-19</div>
                <div class="siimple--float-right">
                    <a href="<?php echo site_url('defcont/andamento'); ?>" class="siimple-navbar-item">Andamento nazionale</a>
                    <a href="<?php echo site_url('defcont/regioni'); ?>" class="siimple-navbar-item">Regioni</a>
                    <a href="<?php echo site_url('defcont/province'); ?>" class="siimple-navbar-item">Province</a>
                </div>
            </div>
        </navbar>
        <main>
            <div class="siimple-content theme-content siimple-content--large">
                <div class="siimple-card">
                    <div class="siimple-card-header siimple--text-center">
                        Scegli una regione
                    </div>
                    <div class="siimple-card-body">
                        <select class="siimple-select siimple-select--fluid" id="regione">
                            <!--<option selected>Seleziona una regione</option>-->
                            <?php foreach($prov as $pr) : ?>
                            <option><?=$pr ?></option>
                            <?php endforeach ?>
                        </select>
                        <br><br>
                        <div class="siimple-btn siimple-btn--primary siimple-btn--fluid" id="conferma">
                            Mostra grafico
                        </div>
                    </div>
                </div>
                <div class="siimple-card">
                    <div class="siimple-card-header siimple--text-center">
                        Totale casi per provincia
                    </div>
                    <div class="siimple-card-body">
                        <div id="positivi" style="height: 450px; width: 100%;"></div>
                    </div>
                </div>
                <blockquote class="siimple-blockquote">
                    Dati aggiornati il <?= $data ?>
                </blockquote>
            </div>
        </main>
    </body>
</html>