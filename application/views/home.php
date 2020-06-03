<html>
    <head>
        <title>INFO COVID - Italia</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
        <meta http-equiv="Content-type" content="text/html;charset=UTF-8">
        <link rel="icon" href="<?=base_url()?>/favicon.ico" type="image/icon">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/siimple@3.3.1/dist/siimple.min.css">
    </head>
    <body>
        <navbar>
            <div class="siimple-navbar siimple-navbar--large siimple-navbar--light animated slideInDown">
                <a href="<?php echo site_url('defcont/index'); ?>" class="siimple-navbar-title">INFO COVID</a>
                <div class="siimple-navbar-subtitle">Tecnologia al servizio della salute</div>
            </div>
        </navbar>
        <main>
            <div class="siimple-content theme-content siimple-content--large">
                <div class="siimple-box">
                    <div class="siimple-box-title">INFO COVID</div>
                    <div class="siimple-box-subtitle">Tecnologia al servizio della salute</div>
                    <div class="siimple-box-detail">Dati italiani sulla diffusione del COVID-19</div>
                </div>
                <div class="siimple-grid animated zoomIn">
                    <div class="siimple-grid-row">
                        <div class="siimple-grid-col siimple-grid-col--4 siimple-grid-col--sm-12">
                            <div class="siimple-card" style="max-width:300px">
                                <div class="siimple-card-header">
                                    Dati andamento nazionale
                                </div>
                                <div class="siimple-card-body">
                                    Scopri l'andamento dell'Italia, come contagiati totali e numero di guariti
                                </div>
                                <div class="siimple-card-footer">
                                    <a href="<?php echo site_url('defcont/andamento'); ?>" class="siimple-btn siimple-btn--primary siimple-btn--fluid">Scopri</a>
                                </div>
                            </div>
                        </div>
                        <div class="siimple-grid-col siimple-grid-col--4 siimple-grid-col--sm-12">
                            <div class="siimple-card" style="max-width:300px">
                                <div class="siimple-card-header">
                                    Dati per regione
                                </div>
                                <div class="siimple-card-body">
                                    Scopri i dati per ogni singola regione, con grafici e mappa
                                </div>
                                <div class="siimple-card-footer">
                                <a href="<?php echo site_url('defcont/regioni'); ?>" class="siimple-btn siimple-btn--primary siimple-btn--fluid">Scopri</a>
                                </div>
                            </div>
                        </div>
                        <div class="siimple-grid-col siimple-grid-col--4 siimple-grid-col--sm-12">
                            <div class="siimple-card" style="max-width:300px">
                                <div class="siimple-card-header">
                                    Dati per provincia <span class="siimple-tag siimple-tag--error siimple-tag--rounded">Preview</span>
                                </div>
                                <div class="siimple-card-body">
                                    Scopri i dati di ogni singola provincia, con grafici
                                </div>
                                <div class="siimple-card-footer">
                                <a href="<?php echo site_url('defcont/province'); ?>" class="siimple-btn siimple-btn--primary siimple-btn--fluid">Scopri</a>
                                </div>
                            </div>
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