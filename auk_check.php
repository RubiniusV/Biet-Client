<?php
$apikey='9961276371637'; #Ich
#$apikey='9999999826532';
$params="apikey=$apikey";

function api_call($params) {
  $query="http://dl6mhw.de/~corona/markt/mservice.php?$params";
  $lines = implode(file($query));
  return($lines);
}

$produkt="Bier";


print "\n\n";

print "Guthaben: ";
print api_call($params."&cmd=guthaben")."\n";

print "\n\n";

print "Klopapier-Auktionen: ";
$result_k= api_call($params."&cmd=angebot&produkt=Klopapier");
print "Result:$result_k\n";
$restminuten=0;
$preis=0;
if (preg_match('/preis=([0-9]+);/',$result_k,$m)) { $preis=$m[1];}
if (preg_match('/minuten=([0-9]+)/',$result_k,$m)) { $restminuten=$m[1];}
print "Aktueller Preis $preis noch $restminuten\n";

print "\n\n";

print "Nudel-Auktionen: ";
$result_r= api_call($params."&cmd=angebot&produkt=Nudeln");
print "Result:$result_r\n";
$restminuten=0;
$preis=0;
if (preg_match('/preis=([0-9]+);/',$result_r,$m)) { $preis=$m[1];}
if (preg_match('/minuten=([0-9]+)/',$result_r,$m)) { $restminuten=$m[1];}
print "Aktueller Preis $preis noch $restminuten\n";

print "\n\n";

print "Bier-Auktionen: ";
$result_b= api_call($params."&cmd=angebot&produkt=$produkt");
print "Result:$result_b\n";
$restminuten=0;
$preis=0;
if (preg_match('/preis=([0-9]+);/',$result_b,$m)) { $preis=$m[1];}
if (preg_match('/minuten=([0-9]+)/',$result_b,$m)) { $restminuten=$m[1];}
print "Aktueller Preis $preis noch $restminuten\n";

print "\n\n";




/*
$string= "preis=2562;ende=2020-05-03 14:44:04;minuten=1";
preg_match ('/ (.+?);/',$result_k,$y);
$zielzeit = $y[1];
#print_r  ($y[1]);
#print_r ($y);
#print "$zielzeit\n";
#$heute = date('H:i:s');
#echo $heute;

#print "\n\n";
#print "\n\n";
#print "\n\n";

preg_match ('/(.+?):/',$zielzeit,$z_h);
preg_match ('/:(.+?):/',$zielzeit,$z_m);
#preg_match ('/:(.+?)/',$zielzeit,$z_s);
$ziel_h = $z_h[1];
$ziel_m = $z_m[1];
#$ziel_s = $z_s[1];
$ziel_s = substr("$zielzeit", -2);
print "Stunden: ";
print "$ziel_h\n";

print "Minuten: ";
print "$ziel_m\n";

print "Sekunden: ";
print "$ziel_s\n";

print "\n\n";
print "\n\n";


$jetzt = date("H:i:s");
print "Uhrzeit :";
print "$jetzt";

preg_match ('/(.+?):/',$jetzt,$j_h);
preg_match ('/:(.+?):/',$jetzt,$j_m);
#preg_match ('/:(.+?)/',$jetzt,$j_s);
$jetzt_h = $j_h[1];
$jetzt_m = $j_m[1];
#$jetzt_s = $j_s[1];
$jetzt_s = substr("$jetzt", -2);
print "\n";
print "Stunden: ";
print "$jetzt_h\n";

print "Minuten: ";
print "$jetzt_m\n";

print "Sekunden: ";
print "$jetzt_s\n";

print "\n\n";

$restsek_k = ($ziel_h*60*60-$jetzt_h*60*60)+($ziel_m*60-$jetzt_m*60)+($ziel_s-$jetzt_s);
print "Restsekunden: ";
print ($restsek_k);

print "\n\n";
*/


#print api_call($params."&cmd=gewonnen");
$gewonnen = api_call($params."&cmd=gewonnen");

print "\n\n";

if (empty($gewonnen)){
print "nichts gewonnen";
}else{print "gewonnen: \n"; print api_call($params."&cmd=gewonnen");}

print "\n\n";

$myfile = fopen("abfragen_test.txt", "r+") or die("Unable to open file!");
#$abfragen = fread($myfile,(filesize("abfragen_test.txt")+1));
#$abfragen = fgets($myfile);
$abfragen=file_get_contents('abfragen_test.txt');
$abfragen = $abfragen + 1;
print $abfragen;
fwrite($myfile, $abfragen);
fclose($myfile);

print "\n\n";


print "Durchschnittspreise: \n";


$dp_seite = file_get_contents('http://dl6mhw.de/~corona/sasi/leader.php');



$lines = explode(">", $dp_seite);
#var_dump($lines);
$dp_bier = intval(substr($lines [20], 26));
$dp_klopapier = intval(substr($lines [22], 30));
$dp_nudeln = intval(substr($lines [21], 27));

 
#preg_match('/Bier: (.+)<br>/', $lines[20], $matches_b);
#preg_match('/Klopapier: (.+)<br>/', $dp_seite, $matches_k);
#preg_match('/Nudeln: (.+)<br>/', $dp_seite, $matches_n);
#$dp_bier = $matches_b[1];
#$dp_klopapier = $matches_k[1];
#$dp_nudeln = $matches_n[1];

#print_r ($matches);
print "Bier: ";
print $dp_bier;
print "\n";

print "Klopapier: ";
print $dp_klopapier;
print "\n";

print "Nudeln: ";
print $dp_nudeln;
print "\n";



/*
require_once('PHPMailer/class.phpmailer.php');
require_once('PHPMailer/class.smtp.php');
require_once('smtpconfig.php');

$mail = new PHPMailer(); 

$mail->IsSMTP();
$mail->Host       = $mailhost;
$mail->SMTPDebug  = 1; // Kann man zu debug Zwecken aktivieren
$mail->SMTPAuth   = true;
$mail->Port       = 587;  
$mail->SMTPSecure = "tls"; 
$mail->Username   = $mailusername;
$mail->Password   = $mailpassword;

$frommail = "tillmax@gmx.de";
$fromname = "Hans Peter";
$mail->SetFrom($frommail, $fromname);

$address = "tillmax1@gmx.de";
$adrname = "Ruben";
$mail->AddAddress($address, $adrname);

$mail->Subject = "Test";
$mail->Body = "Der Test";

if(!$mail->Send()) {
  echo "Mailer Error: " . $mail->ErrorInfo;
} else {
  echo "Message sent!";
}
#$msg = "Dies ist ein Test";

#mail("tillmax1@gmx.de","Test",$msg);

*/


?>