<?php
# die genutzte Schnittstelle basiert auf http-get aufrufen und einem
# proprietären Protokoll
# es funktioniert - zeigt aber auch dass Standardisierung sinnvoll ist
$apikey = '9961276371637'; #Ich
$params = "apikey=$apikey";
date_default_timezone_set('Europe/Berlin');
$produkt = "Nudeln";

#$restminuten=2;
#$restsekunden = $restminuten * 6;
#print "Schlafen fuer $restsekunden Sekunden";
#sleep($restsekunden);


start:

    print "\n";
    print "NEUSTART: ";
    print $produkt;
    print "\n";

    #1. Abfrage/Anzeige des Guthabens
    print "Guthaben: ";
    print api_call($params . "&cmd=guthaben") . "\n";
    $guthaben_str = api_call($params . "&cmd=guthaben") . "\n";
    $guthaben = intval($guthaben_str);

    print "\n";

    #8. Resttage errechnen
    $enddatum = mktime(0, 0, 0, 07, 07, 2020);
    $today = time();
    $difference = $enddatum - $today;
    if ($difference < 0)
    {
        $difference = 0;
    }

    $resttage = floor($difference / 60 / 60 / 24);

    #echo "Noch ". floor($difference/60/60/24)." Tage bis zum Ende.";
    echo "Noch ", $resttage, " Tage bis zum Ende.";

    print "\n";

    #9 Tagespreis ermitteln
    $tagespreis = floor(($guthaben / ($resttage * 5)));
    print "Tagespreis: ";
    echo $tagespreis;

    print "\n";

    #5. Abfrage der gewonnenen Auktionen
    print "Gewonnen:\n";
    if (empty($gewonnen))
    {
        print "Nichts gewonnen!";
    }
    else
    {
        print api_call($params . "&cmd=gewonnen");
    }
    print "\n";
    #print "Gewonnen:\n";
    #print api_call($params."&cmd=gewonnen");
    #print "\n";
    

    #6. Berechnen des Preisdurchschnitts aus allen gewonnenen Auktionen.
    #$string = "Bier;3582;2020-05-03 14:44:04 Bier;1234;2020-05-03 14:44:04 Bier;4444;2020-05-03 14:44:04 Bier;4322;2020-05-03 14:44:04";
    $res = api_call($params . "&cmd=gewonnen");
    if ($res)
    {
        preg_match_all('/;(.+?);/', $res, $x);
        $vic_num = count($x[1]);
        print "Anzahl der gewonnen Aktionen: ";
        print "$vic_num";
        print "\n";
        #print_r ($x[1]);
        

        $x_int = array_map('intval', $x[1]);

        $avg_array = array_filter($x_int);
        if (count($avg_array))
        {
            $avg = array_sum($avg_array) / count($avg_array);
        }
        print "Durchschnittspreis: ";
        print "$avg\n";

    }

    print "Durchschnittspreis Nudeln: ";
    $dp_seite = file_get_contents('http://dl6mhw.de/~corona/sasi/leader.php');

    $lines = explode(">", $dp_seite);
    if ($produkt == "Bier") 
    {
    $dp_bier = intval(substr($lines [20], 26));
    } elseif ($produkt == "Klopapier") 
    {
    $dp_klopapier = intval(substr($lines [22], 30));
    } elseif ($produkt == "Nudeln") 
    {
    $dp_nudeln = intval(substr($lines [21], 27));
    }

    #preg_match('/Nudeln: (.+)<br>/', $dp_seite, $matches_b);
    #$dp_nudeln = $matches_b[1];
    #print $dp_nudeln;
    #print "\n";

    $test = 0;

    abfrage:

        print "\n\n";

        #2. Abfrage ob Bier im Angebot ist mit Auswertung (nur Preis)
        $myfile = fopen("abfragen_b.txt", "r+") or die("Unable to open file!");
        $abfragen = file_get_contents('abfragen_b.txt');
        $abfragen = $abfragen + 1;
        print "Beginne Abfrage #";
        print $abfragen;
        fwrite($myfile, $abfragen);
        fclose($myfile);

        print "\n\n";

        $result = api_call($params . "&cmd=angebot&produkt=$produkt");
        print "Result:$result\n";

        if ($result != 'nix')
        {

            $restminuten = 0;
            $preis = 0;
            if (preg_match('/preis=([0-9]+);/', $result, $m))
            {
                $preis = $m[1];
            }
            if (preg_match('/minuten=([0-9]+)/', $result, $m))
            {
                $restminuten = $m[1];
            }
            $restsekunden = $restminuten * 60;
            print "Aktueller Preis $preis noch $restminuten (Sekunden: $restsekunden) \n";

            #7. Errechnen der Restsekunden
            

            $result = api_call($params . "&cmd=angebot&produkt=$produkt");

            preg_match('/ (.+?);/', $result, $y);
            $zielzeit = $y[1];

            preg_match('/(.+?):/', $zielzeit, $z_h);
            preg_match('/:(.+?):/', $zielzeit, $z_m);
            $ziel_h = $z_h[1];
            $ziel_m = $z_m[1];
            $ziel_s = substr("$zielzeit", -2);
            #print "Stunden: ";
            #print "$ziel_h\n";
            #print "Minuten: ";
            #print "$ziel_m\n";
            #print "Sekunden: ";
            #print "$ziel_s\n";
            #print "\n\n";
            $jetzt = date("H:i:s");

            if (preg_match('/ende=(.*);/', $result, $m)) { 
                $ende=$m[1];
                $restsekunden = abs(time()-strtotime("$ende"));
                }

                preg_match('/(.+?):/', $jetzt, $j_h);
                preg_match('/:(.+?):/', $jetzt, $j_m);
                $jetzt_h = $j_h[1];
                $jetzt_m = $j_m[1];
                $jetzt_s = substr("$jetzt", -2);

                print "\n\n";

                /*
                $restsek = ($ziel_h * 60 * 60 - $jetzt_h * 60 * 60) + ($ziel_m * 60 - $jetzt_m * 60) + ($ziel_s - $jetzt_s);
                if ($restsek < 0) {
                    #$restsek = ($jetzt_h * 60 * 60 - $ziel_h * 60 * 60) + ($jetzt_m * 60 - $ziel_m * 60) + ($jetzt_s - $ziel_s);
                    #hours to midnight
                    $htm = (24*60*60) - ($jetzt_h * 60 * 60) - ($jetzt_m * 60) - $jetzt_s;
                    print "Noch  $htm  bis Mitternacht \n";
                    #print ($htm);
                    print ("heute keine mehr");
                    sleep($htm);
                }
                */

                #print "Restsekunden: ";
                #print ($restsek);
                
                print "\n\n";

            # wenn nicht führend, dann Gebot absetzen
            if ($fuehrt != 'OK')
            {

                $safe_check = api_call($params . "&cmd=angebot&produkt=$produkt");
                if (preg_match('/preis=([0-9]+);/', $safe_check, $m))
                {
                    $preis = $m[1];
                }

                #$result= api_call($params."&cmd=angebot&produkt=$produkt");
                preg_match('/ (.+?);/', $safe_check, $y);
                $zielzeit = $y[1];

                preg_match('/(.+?):/', $zielzeit, $z_h);
                preg_match('/:(.+?):/', $zielzeit, $z_m);
                $ziel_h = $z_h[1];
                $ziel_m = $z_m[1];
                $ziel_s = substr("$zielzeit", -2);

                $jetzt = date("H:i:s");

                if (preg_match('/ende=(.*);/', $result, $m)) { 
                $ende=$m[1];
                $restsek = abs(time()-strtotime("$ende"));
                }

                preg_match('/(.+?):/', $jetzt, $j_h);
                preg_match('/:(.+?):/', $jetzt, $j_m);
                $jetzt_h = $j_h[1];
                $jetzt_m = $j_m[1];
                $jetzt_s = substr("$jetzt", -2);

                print "\n\n";
                
                /*
                $restsek = ($ziel_h * 60 * 60 - $jetzt_h * 60 * 60) + ($ziel_m * 60 - $jetzt_m * 60) + ($ziel_s - $jetzt_s);
                if ($restsek < 0) {
                    #$restsek = ($jetzt_h * 60 * 60 - $ziel_h * 60 * 60) + ($jetzt_m * 60 - $ziel_m * 60) + ($jetzt_s - $ziel_s);
                    #hours to midnight
                    $htm = (24*60*60) - ($jetzt_h * 60 * 60) - ($jetzt_m * 60) - $jetzt_s;
                    print "Noch  $htm  bis Mitternacht \n";
                    #print ($htm);
                    print ("heute keine mehr");
                    sleep($htm);
                }
                */

                #print "Restsekunden: ";
                #print ($restsek);
                print "\n\n";  

                #3. test ob eigenes Angebot führt
                $fuehrt = api_call($params . "&cmd=status&produkt=$produkt");
                print "Status führt:$fuehrt\n"; 

                if ($restsek > 10)
                {
                    $schlaf = ($restsek - 3);
                    print "Nur noch $schlaf Sekunden Mami";
                    print "\n\n";
                    sleep($schlaf);
                }

                $safe_check = api_call($params . "&cmd=angebot&produkt=$produkt");
                if (preg_match('/preis=([0-9]+);/', $safe_check, $m))
                {
                    $preis = $m[1];
                }

                #4. Neues Gebot
                if (($preis < $dp_nudeln) and ($preis <50000))
                {
                    if ($preis < ($dp_nudeln / 2))
                    {
                        $npreis = $preis + 10000;
                        print "C Gebot waere: ";
                        print $npreis;
                        print "\n\n";
                        print "Gebot $npreis abgegeben:" . api_call($params . "&cmd=bieten&produkt=$produkt&preis=$npreis") . "\n";
                    }
                    else
                    {
                        $npreis = $preis +5000;
                        print "C Gebot waere: ";
                        print $npreis;
                        print "\n\n";
                        print "Gebot $npreis abgegeben:" . api_call($params . "&cmd=bieten&produkt=$produkt&preis=$npreis") . "\n";
                    }

                } 
                else 
                { 
                	$npreis = 0;
                	print $preis;
                	print " ist zu teuer!"; 
                }

            }
            else
            {
                print "Bin Höchstbieter\n";
                sleep($restsek_k-5);
                goto abfrage;
            }

            print "\n\n";

        }
        else
        {

            if ($test < 3)
            {
                $test = $test + 1;
                print "Keine Auktion vorhanden!\n";
                print "ICH GEHE SCHLAFEN. WENN ICH WIEDER DA BIN, IST DAS GEFAELLIGST ANDERS!\n";
                print "Uhrzeit: ";
                print date("H:i:s");
                print "\n\n";
                sleep(900);
                goto abfrage;

            }
            else
            {
                goto start;
            }

        }
        goto start;
        #Basisaufruf
        function api_call($params)
        {
            $query = "http://dl6mhw.de/~corona/markt/mservice.php?$params";
            $lines = implode(file($query));
            return ($lines);
        }
?>
