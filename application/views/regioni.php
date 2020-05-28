<html>
    <head>
        <title>INFO COVID - Italia - Regioni</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
        <link rel="icon" href="<?=base_url()?>/favicon.ico" type="image/icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/siimple@3.3.1/dist/siimple.min.css">
        <script src="https://js.api.here.com/v3/3.1/mapsjs-core.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://js.api.here.com/v3/3.1/mapsjs-service.js" type="text/javascript" charset="utf-8"></script>
        <script src="https://js.api.here.com/v3/3.1/mapsjs-ui.js" type="text/javascript" charset="utf-8"></script>
        <script type="text/javascript" src="https://js.api.here.com/v3/3.1/mapsjs-mapevents.js"></script>
        <link rel="stylesheet" type="text/css" href="https://js.api.here.com/v3/3.1/mapsjs-ui.css" />
    </head>
    <body>
    <script>

        function addMarkerToGroup(group, coordinate, html) {
            var marker = new H.map.Marker(coordinate);
            // add custom data to the marker
            marker.setData(html);
            group.addObject(marker);
        }

        /**
        * Add two markers showing the position of regions
        * Clicking on a marker opens an infobubble which holds HTML content related to the marker.
        * @param  {H.Map} map      A HERE Map instance within the application
        */
        function addInfoBubble(map, ui) {
            var group = new H.map.Group();

            map.addObject(group);

            // add 'tap' event listener, that opens info bubble, to the group
            group.addEventListener('tap', function (evt) {
                // event target is the marker itself, group is a parent event target
                // for all objects that it contains
                var bubble =  new H.ui.InfoBubble(evt.target.getGeometry(), {
                // read custom data
                content: evt.target.getData()
                });
                // show info bubble
                ui.addBubble(bubble);
            }, false);

            var dati = <?php echo json_encode($regioni)?>;

            dati.forEach(function (item, index) {
                addMarkerToGroup(group, {lat:item.lat, lng:item.lon},
                item.denominazione_regione + ': ' + item.totale_casi);
            });
        }
        window.onload = function () { 
            var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                theme: "light2",
                title:{
                    text: "Totale casi per regione"
                },
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
                    /*name: "Regioni",*/
                    indexLabel: "{y}",
                    /*yValueFormatString: "",
                    showInLegend: true,*/
                    dataPoints: <?php echo json_encode($graph, JSON_NUMERIC_CHECK); ?>
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

            // Codice per la mappa

            // Initialize the platform object:
            var platform = new H.service.Platform({
                'apikey': 'JEhVRCFs-V9FIGLsDXmil9qHnDFKjaGcXMOs-muUooA'
            });

            // Obtain the default map types from the platform object
            var maptypes = platform.createDefaultLayers();

            // Instantiate (and display) a map object:
            var map = new H.Map (
                document.getElementById('mapContainer'),
                maptypes.vector.normal.map,
                {
                    zoom: 5.2,
                    center: { lng: 12.6, lat: 42.4 },
                    pixelRatio: window.devicePixelRatio || 1
                }
            );

            var behavior = new H.mapevents.Behavior(new H.mapevents.MapEvents(map));
            var ui = H.ui.UI.createDefault(map, maptypes);
            addInfoBubble(map, ui); 
        }
    </script>
        <navbar>
            <div class="siimple-navbar siimple-navbar--large siimple-navbar--light animated slideInDown">
                <a href="<?php echo site_url('defcont/index'); ?>" class="siimple-navbar-title">INFO COVID</a>
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
                <div class="siimple-grid animated zoomIn">
                    <div class="siimple-grid-row">
                        <div class="siimple-grid-col siimple-grid-col--3 siimple-grid-col--sm-12">
                            <div class="siimple-h4">Totale casi</div>
                            <div class="siimple-list">
                                <?php foreach($regioni as $regione) : ?>
                                <div class="siimple-list-item siimple--clearfix">
                                    <div class="siimple--float-left">
                                        <div class="siimple-list-title"><?= $regione->denominazione_regione ?></div>
                                    </div>
                                    <div class="siimple--float-right"><?= $regione->totale_casi ?></div>
                                </div>
                                <?php endforeach?>
                            </div>
                        </div>
                        <div class="siimple-grid-col siimple-grid-col--9 siimple-grid-col--sm-12">
                            <div id="chartContainer" style="height: 450px; width: 100%;"></div>
                            <script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>
                            <br><br>
                            <div style="width: 100%; height: 480px" id="mapContainer"></div>
                        </div>
                    </div>
                </div>
                <blockquote class="siimple-blockquote">
                    Dati aggiornati il <?= $data ?>
                </blockquote>
            </div>
        </main>
    </body>
</html>