<?php include "funzioni.php"; ?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolo codice fiscale</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="codice_fiscale">
        <div>
            <h1>Calcolo codice fiscale</h1>
        </div>        
        <form action="#" method="POST">
            <div class="full">
                <label for="nome">Nome</label>
                <input type="text" name="nome">
            </div>
            <div class="_75">
                <label for="cognome">Cognome</label>
                <input type="text" name="cognome">
            </div>
            <div class="_25">
                <label for="sesso">Sesso:</label>
                <select id="sesso" name="sesso">
                    <option value="M">Uomo</option>
                    <option value="F">Donna</option>
                </select>
            </div>
            <div class="full">
                <label for="luogo_di_nascita">Luogo di nascita</label>
                <select id="luogo_di_nascita" name="luogo_di_nascita">
                    <?php
                    $comuni = leggi_comuni_da_csv("comuni.csv");
                    foreach ($comuni as $comune) {
                        echo "<option value=\"$comune\">$comune</option>";
                    }
                    ?>
                </select>
            </div>

            <div id="data" class="full">
                <div>
                    <label for="giorno">Giorno:</label>
                    <select id="giorno" name="giorno">
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label for="mese">Mese:</label>
                    <select id="mese" name="mese">
                        <?php
                        for ($i = 1; $i <= 12; $i++) {
                            $formattedMonth = str_pad($i, 2, "0", STR_PAD_LEFT); 
                            echo "<option value=\"$formattedMonth\">$formattedMonth</option>";
                        }
                        ?>
                    </select>
                </div>

                <div>
                    <label for="anno">Anno:</label>
                    <select id="anno" name="anno">
                        <?php
                        $currentYear = date("Y");
                        for ($i = $currentYear; $i >= $currentYear - 100; $i--) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>
            <div>
                <input type="submit" name="calcola" value="Calcola">
            </div>
            <div>
                <p>Vuoi conoscere l'algoritmo per calcolare il codice fiscale? Puoi farlo attraverso il portale dell'Agenzia dell'Entrate tramite questo <a href="https://www.agenziaentrate.gov.it/portale/web/guest/schede/istanze/richiesta-ts_cf/informazioni-codificazione-pf#:~:text=Le%20prime%20sette%20cifre%20rappresentano,il%20carattere%20numerico%20di%20controllo." target="_blank">link</a>.</p>
            </div>
            <?php
                if(isset($_POST["calcola"])) {
                    $data = $_POST["anno"]."-".$_POST["mese"]."-".$_POST["giorno"];
                    $codice = calcola_codice_fiscale( $_POST["nome"], $_POST["cognome"], $data, $_POST["luogo_di_nascita"], $_POST["sesso"] );
                    echo "<div><p id='risultato'>Codice fiscale: {$codice}</p></div>";
                }
            ?>
        </form>
    </div> 

</body>
</html>