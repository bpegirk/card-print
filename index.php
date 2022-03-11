<?php

require '../../composer/vendor/autoload.php';

$list = require('list.php');

$pdf = new TCPDF('P', "mm", "A4", true, 'UTF-8', false);
$pdf->SetCreator("s");
$pdf->SetTitle('s');
$pdf->SetAutoPageBreak(false, 0);
$pdf->SetMargins(0, 0, 0);
$pdf->SetDisplayMode('fullpage');
$pdf->setPrintHeader(false);
$pdf->setCellHeightRatio(1.5);
$pdf->setPrintFooter(false);

$pdf->AddPage("P", "A4");
$pdf->setJPEGQuality(100);

$startPosX = 4;
$startPosY = 4;
$x = $startPosX;
$y = $startPosY;
$w = 48;
$h = 70;
$column = 4;
$row = round(count($list) / $column) + 1;


function drawImage($params)
{
    $rightSpace = 140;
    $startFontSize = 120;
//    $bgImage = new Imagick('images/ek.png');
    $bgImage = new Imagick('images/'.$params['t'].'.png');
    $draw = new ImagickDraw();
    $draw->setFillColor('none');
    $draw->setStrokeColor(new ImagickPixel('black'));
    $draw->setStrokeWidth(1);
    $draw->setStrokeAntialias(false);
    $draw->rectangle(0, 0, $bgImage->getImageWidth() - 1, $bgImage->getImageHeight() - 1);
    $draw->setStrokeWidth(0);


    $draw->setGravity(Imagick::GRAVITY_WEST);
    $draw->setFont('fonts/Mayak-CondensedMedium.ttf');
    $draw->setStrokeColor('none');
    $draw->setFillColor('white');
    $draw->setStrokeAntialias(true);
    $draw->setTextAntialias(true);

    //Рисуем группу, с поправками на длину текста
    $fontSize = $startFontSize;
    do {
        $draw->setFontSize($fontSize);
        $textWidth = $bgImage->queryFontMetrics($draw, $params['r']);
        $fontSize -= 2;
    } while ($textWidth['textWidth'] > $bgImage->getImageWidth() - $rightSpace);
    $draw->annotation(80, 220, $params['r']);

    $draw->setFillColor('black');

    //Рисуем Фамилию, с поправками на длину текста
    $fontSize = $startFontSize;
    do {
        $draw->setFontSize($fontSize);
        $textWidth = $bgImage->queryFontMetrics($draw, $params['f']);
        $fontSize -= 2;
    } while ($textWidth['textWidth'] > $bgImage->getImageWidth() - $rightSpace);
    $draw->annotation(80, -350, $params['f']);

    //Рисуем Имя, с поправками на длину текста
    $fontSize = $startFontSize;
    do {
        $draw->setFontSize($fontSize);
        $textWidth = $bgImage->queryFontMetrics($draw, $params['i']);
        $fontSize -= 2;
    } while ($textWidth['textWidth'] > $bgImage->getImageWidth() - $rightSpace);
    $draw->annotation(80, -235, $params['i']);

    //Рисуем Отчество, с поправками на длину текста
    $fontSize = $startFontSize;
    do {
        $draw->setFontSize($fontSize);
        $textWidth = $bgImage->queryFontMetrics($draw, $params['o']);
        $fontSize -= 2;
    } while ($textWidth['textWidth'] > $bgImage->getImageWidth() - $rightSpace);
    $draw->annotation(80, -110, $params['o']);

    $bgImage->setImageFormat("png");
    $bgImage->drawImage($draw);
//    $bgImage->rotateImage('', 90);

    header("Content-Type: image/png");
    return $bgImage->getImageBlob();
}


$numImage = 0;
foreach ($list as $param) {
    $photo = drawImage($param);

    $pdf->Image('@'.$photo, $x, $y, $w, $h, 'PNG', '', '', true, 600);
    $numImage++;
    if ($numImage % 4 == 0) {
        $x = $startPosX;
        $y += $h + 1; // new row
    } else {
        $x += $w + 1; // new column
    }
    if ($numImage > 15) {
        $pdf->AddPage("P", "A4");
        $numImage = 0;
        $x = $startPosX;
        $y = $startPosY;
    }
}
//
//for ($i = 1; $i < $row + 1; ++$i) {
//    for ($j = 0; $j < $column; ++$j) {
//        $photo = drawImage()
//        $pdf->Image('@'.$photos[$numImage], $x, $y, $w, $h, 'PNG', '', '', true, 600);
//        $x += $w + 0.5; // new column
//        $numImage++;
//    }
//    $x = 0;
//    $y += $h + 0.5; // new row
//    if ($numImage % 18 == 0 && $numImage != 0 && $numImage != count($photos)) {
//        $pdf->AddPage("P", "A4");
//        $x = 0;
//        $y = 0;
//    }
//}

$pdf->Output("photo_stickers.pdf", "I");