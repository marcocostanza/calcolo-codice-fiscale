<?php

function calcola_codice_fiscale( $nome, $cognome, $data_nascita, $comune_nascita, $sesso ) {
    $cognome_codice = calcola_codice_cognome($cognome);
    $nome_codice = calcola_codice_nome($nome);
    $data_nascita_codice = calcola_codice_data( $data_nascita, $sesso ) ; 
    $comune_nascita_codice = calcola_codice_comune( $comune_nascita );
    $codice_controllo = calcola_carattere_controllo($cognome_codice . $nome_codice . $data_nascita_codice . $comune_nascita_codice );
    return $cognome_codice . $nome_codice . $data_nascita_codice . $comune_nascita_codice . $sesso_codice . $codice_controllo;
}

function calcola_codice_data($data, $sesso) {
    $data = new DateTime($data);
    $anno = $data->format('y');
    $mese = $data->format('n');
    $giorno = $data->format('j');
    
    // Codifica dei mesi
    $mese_codice = array(
        1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'H', 7 => 'L',
        8 => 'M', 9 => 'P', 10 => 'R', 11 => 'S', 12 => 'T'
    );
    
    if ($sesso == 'F') {
        $giorno += 40;
    }

    $codice = $anno . $mese_codice[$mese] . str_pad($giorno, 2, '0', STR_PAD_LEFT);
    
    return $codice;
}

function calcola_codice_cognome( $cognome ) {
    // Rimuovo eventuali spazi, trattini e apostrofi dal cognome
    $cognome = str_replace( ' ', '', $cognome );
    $cognome = str_replace( '-', '', $cognome );
    $cognome = str_replace( '\'', '', $cognome );

    $lettere_accentate = array(
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
    );
    
    $cognome = strtr($cognome, $lettere_accentate);
    $cognome = strtoupper( $cognome );

    // Controlla che il cognome non sia vuoto
    if ( empty( $cognome ) ) {
        return 'XXX';
    }

    // Controlla che il cognome sia composto solo da lettere dell'alfabeto
    if ( ! preg_match( '/^[A-Z]+$/', $cognome ) ) {
        return 'XXX';
    }

    // Rimuovo eventuali vocali dal cognome
    $cognome_consonanti = preg_replace( '/[AEIOU]/', '', $cognome );

    // Se il cognome è composto da almeno tre consonanti, prendi le prime tre
    if ( strlen( $cognome_consonanti ) >= 3 ) {
        $codice = substr( $cognome_consonanti, 0, 3 );
    }
    // Se il cognome è composto da meno di tre consonanti
    else {
        $vocali = preg_replace( '/[^AEIOU]/', '', $cognome );
        $codice = substr($cognome_consonanti . $vocali . 'XXX', 0, 3);
    }

    // Restituisco il codice del cognome
    return $codice;
}

function calcola_codice_nome($nome) {
    $vocali = array('A', 'E', 'I', 'O', 'U');
    $consonanti = array_diff(range('A', 'Z'), $vocali);
    $nome = str_replace(' ', '', $nome);
    $nome = str_replace('-', '', $nome);
    $nome = str_replace('\'', '', $nome);
    $nome = strtoupper($nome);

    $lettere_accentate = array(
        'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
        'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
        'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
        'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o',
        'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
    );
    
    $nome = strtr($nome, $lettere_accentate);

    $lunghezza = strlen($nome);

    $codice = '';

    // Ottengo tutte le consonanti del nome
    $consonanti_nome = '';
    for ($i = 0; $i < $lunghezza; $i++) {
        if (in_array($nome[$i], $consonanti)) {
            $consonanti_nome .= $nome[$i];
        }
    }

    // Seleziono le consonanti nel codice in base alla loro presenza nel nome
    if (strlen($consonanti_nome) > 3) {
        $codice .= $consonanti_nome[0] . $consonanti_nome[2] . $consonanti_nome[3];
    } elseif (strlen($consonanti_nome) == 3) {
        $codice .= $consonanti_nome;
    } else {
        $codice .= $consonanti_nome;
        $nome_senza_consonanti = str_replace($consonanti, '', $nome);
        $codice .= substr($nome_senza_consonanti, 0, 3 - strlen($consonanti_nome));
    }
    
    $codice = strtoupper($codice);

    return $codice;
}

function calcola_codice_comune($comune) {
    $file = fopen("comuni.csv", "r");

    while ( ( $row = fgetcsv( $file ) ) !== false ) {
        // Se il nome del comune corrisponde, restituisco il codice comune
        if ( strtoupper( $row[1] ) === strtoupper( $comune ) ) {
            fclose( $file );
            return $row[0];
        }
    }

    fclose( $file );
    return '000';
}

function leggi_comuni_da_csv() {
    $comuni = array();
    if (($handle = fopen("comuni.csv", "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $comuni[] = $data[1];
        }
        fclose($handle);
    }
    return $comuni;
}


function calcola_carattere_controllo($codice) {
    $tabella_pari = array(
      '0' => 0, '1' => 1, '2' => 2, '3' => 3, '4' => 4, '5' => 5, '6' => 6, '7' => 7, '8' => 8, '9' => 9,
      'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7, 'I' => 8, 'J' => 9,
      'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15, 'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19,
      'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23, 'Y' => 24, 'Z' => 25
    );
    $tabella_dispari = array(
      '0' => 1, '1' => 0, '2' => 5, '3' => 7, '4' => 9, '5' => 13, '6' => 15, '7' => 17, '8' => 19, '9' => 21,
      'A' => 1, 'B' => 0, 'C' => 5, 'D' => 7, 'E' => 9, 'F' => 13, 'G' => 15, 'H' => 17, 'I' => 19, 'J' => 21,
      'K' => 2, 'L' => 4, 'M' => 18, 'N' => 20, 'O' => 11, 'P' => 3, 'Q' => 6, 'R' => 8, 'S' => 12, 'T' => 14,
      'U' => 16, 'V' => 10, 'W' => 22, 'X' => 25, 'Y' => 24, 'Z' => 23
    );
  
    $s = 0;
    for ($i = 0; $i < 15; $i++) {
      $c = $codice[$i];
      if ($i % 2 == 0) {
        $s += $tabella_dispari[$c];
      } else {
        $s += $tabella_pari[$c];
      }
    }
    $resto = $s % 26;
    $carattere = chr(65 + $resto);
    return $carattere;
}


function codice_catastale($codice_catastale) {
    $file_csv = fopen('comuni.csv', 'r');
    $comune = '';

    while (($row = fgetcsv($file_csv, 0, ",")) !== FALSE) {
        if ($row[0] === $codice_catastale) {
            $comune = $row[1];
            break;
        }
    }

    fclose($file_csv);
    return $comune;
}

?>