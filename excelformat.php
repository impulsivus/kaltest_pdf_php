<?php

require __DIR__ . '/vendor/autoload.php';


// Parse PDF file and build necessary objects.

$parser = new \Smalot\PdfParser\Parser();
$pdf = $parser->parseFile($_GET["file"]);
//unlink($_GET["file"]);

$text = $pdf->getPages()[0]->getText();
$table = explode("Etiket", $text);
unset($table[0]);
$rows = array();

$beton_tipi = "";
$beton_hacmi = 0;
$raporno = "";
$etiketler = array();
$mikserler = array();
$irsaliyeler = array();
$kalip_no = array();
$alinis_saat = array();
$alinis_dk = array();
$sicaklik_ortam = array();
$sicaklik_beton = array();
$beyan_ed_slump = array();
$olculen_slump = array();
$kirilma_yuku = array();
$gorunur_yogunluk = array();
$sonuc_7gun = array();
$sonuc_28gun = array();
$reelsonuc_28gun = array();

foreach ($pdf->getPages() as $page) {
    $pageText = $page->getText();
    preg_match_all("#(\d+)\n  (\d+)\n  (FKT\d+\n \n\d)\n  ([\d \-]+)\n  ([\d]+)\n  ([\d]+)\n  ([\d]+)\n  ([\d]+)\n  ([\w\d]+)\n  ([\d.\w]+)\n  ([\d.\w]+)\n  ([\d.]+)\n  ([\d.]+)#si", $pageText, $matches);
    //var_dump($matches[13]);
    $etiketler = array_merge($etiketler, $matches[1]);
    $mikserler = array_merge($mikserler, $matches[2]);
    $irsaliyeler = array_merge($irsaliyeler, preg_replace("#[\n ]+#", "", $matches[3]));
    $kalip_no = array_merge($kalip_no, $matches[4]);
    $alinis_saat = array_merge($alinis_saat, $matches[5]);
    $alinis_dk = array_merge($alinis_dk, $matches[6]);
    $sicaklik_ortam = array_merge($sicaklik_ortam, $matches[7]);
    $sicaklik_beton = array_merge($sicaklik_beton, $matches[8]);
    $beyan_ed_slump = array_merge($beyan_ed_slump, $matches[9]);
    $olculen_slump = array_merge($olculen_slump, $matches[10]);
    $kirilma_yuku = array_merge($kirilma_yuku, $matches[11]);
    $gorunur_yogunluk = array_merge($gorunur_yogunluk, $matches[12]);
    foreach ($page->getDataTm() as $k => $pageData) {
        if (in_array($pageData[1], $matches[13], true)) {
            if (476 > $pageData[0][4] && $pageData[0][4] > 475) {
                $sonuc_7gun[] = $pageData[1];
                $sonuc_28gun[] = "";
            } else if (519 > $pageData[0][4] && $pageData[0][4] > 518) {
                $sonuc_7gun[] = "";
                $sonuc_28gun[] = $pageData[1];
            }
        }
        if (600 < $pageData[0][5] && $pageData[0][5] < 601 && 87 < $pageData[0][4] && $pageData[0][4] < 88) {
            $beton_tipi = explode("-", $pageData[1])[0];
            $beton_hacmi = explode("-", $pageData[1])[1];
            echo "BETON TİPİ: " . $beton_tipi . "<hr />";
            echo "BETON HACMI: " . $beton_hacmi . "<hr />";
        }
        if ($pageData[1] == "RAPOR NO" && $raporno == "") {
            $raporno = $page->getDataTm()[$k + 1][1];
            echo "RAPOR NO: " . $raporno . "<hr />";
        }
    }
}


$uniq = array_unique($mikserler);
$irsaliye_uniq = array_values(array_unique($irsaliyeler));
$uniq_cnt = count($uniq);

$arr_28g = array();

for ($count_uniq = 1; $count_uniq < sizeof($uniq) + 1; $count_uniq++) {
    $birlestirilecek_keyler = array_keys($mikserler, $count_uniq);
    $arr_tmp_28g = array();
    $arr_tmp_7g = array();
    foreach ($birlestirilecek_keyler as $key) {
        $arr_tmp_28g[] = $sonuc_28gun[$key];
        $arr_tmp_7g[] = $sonuc_7gun[$key];
    }
    $reelsonuc_28gun[$count_uniq] = $arr_tmp_28g;
    $reelsonuc_7gun[$count_uniq] = $arr_tmp_7g;
}


?>




<!--table>
    <tr>
        <th colspan="2">28 Günlük Numune</th>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <?php
    for ($count = 1; $count < count($irsaliye_uniq); $count++) {
        echo "<tr>
        <td>" . $reelsonuc_28gun[$count][1] . "</td><td>" . $reelsonuc_28gun[$count][2] . "</td>
        </tr>
        <tr>
        <td>" . $reelsonuc_28gun[$count][3] . "</td><td>&nbsp;</td>
        </tr>";
    }

    echo "</tr>"; ?>
</table-->


<?php

$kod7g = "";
$kod28g = "";
$irsaliye_list = "";

for ($count = 1; $count < count($irsaliye_uniq) + 1; $count++) {
    $irsaliye_list .= $irsaliye_uniq[$count - 1] . "\n";
    $kod7g .= $reelsonuc_7gun[$count][0] . "\n\t\n";
    $kod28g .= "\t" . $reelsonuc_28gun[$count][1] . "\n" . $reelsonuc_28gun[$count][2] . "\t" . $reelsonuc_28gun[$count][3] . "\n";
}


?>

<script>
    function kopyalairsaliye() {
        var textToCopy = document.getElementById("irsaliye");
        textToCopy.select();
        document.execCommand("copy");
    }

    function kopyala7gun() {
        var textToCopy = document.getElementById("7gunhidden");
        textToCopy.select();
        document.execCommand("copy");
    }

    function kopyala28gun() {
        var textToCopy = document.getElementById("28gunhidden");
        textToCopy.select();
        document.execCommand("copy");
    }

    function tablotoggle7gun() {
        if (document.getElementById("7guntablo").visibility == "hidden") {
            document.getElementById("7guntablo").visibility = "visible";
        } else {
            document.getElementById("7guntablo").visibility = "hidden";
        }
    }
</script>
7 Günlük Numune - <a onclick="kopyalairsaliye()">KOPYALA</a> - <a onclick="tablotoggle7gun()">TABLO</a><br>

<textarea id="irsaliye"><?= $irsaliye_list ?></textarea>
<hr>
7 Günlük Numune - <a onclick="kopyala7gun()">KOPYALA</a> - <a onclick="tablotoggle7gun()">TABLO</a><br>

<textarea id="7gunhidden"><?= $kod7g ?></textarea>
<hr>
28 Günlük Numune - <a onclick="kopyala28gun()">KOPYALA</a> - <a onclick="tablotoggle7gun()">TABLO</a><br>

<textarea id="28gunhidden"><?= $kod28g ?></textarea>

<!--table id="7guntablo">
    <tr>
        <th colspan="2">7 Günlük Numune - <a onclick="kopyala7gun2()">KOPYALA</a> - <a onclick="tablotoggle7gun()">TABLO</a></th>
    </tr>
    <?php
    for ($count = 1; $count < count($irsaliye_uniq); $count++) {
        echo "<tr>
        <td>" . $reelsonuc_7gun[$count][0] . "</td><td>&nbsp;</td>
        </tr>
        <tr>
        <td>&nbsp;</td><td>&nbsp;</td>
        </tr>";
    }

    echo "</tr>"; ?>
</table-->


<div contenteditable="true"></div>