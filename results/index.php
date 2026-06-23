<?php

require_once 'telemetry_db.php';

error_reporting(0);
putenv('GDFONTPATH='.__DIR__);

/**
 * @param string $name
 *
 * @return string|null
 */
function tryFont($name)
{
    if (is_array(imageftbbox(12, 0, $name, 'M'))) {
        return $name;
    }

    $fullPathToFont = __DIR__.'/'.$name.'.ttf';
    if (is_array(imageftbbox(12, 0, $fullPathToFont, 'M'))) {
        return $fullPathToFont;
    }

    return null;
}

/**
 * @param int|float $d
 *
 * @return string
 */
function format($d)
{
    if ($d < 10) {
        return number_format($d, 2, '.', '');
    }
    if ($d < 100) {
        return number_format($d, 1, '.', '');
    }

    return number_format($d, 0, '.', '');
}

/**
 * @param array $speedtest
 *
 * @return array
 */
function formatSpeedtestDataForImage($speedtest)
{
    $speedtest['dl'] = format($speedtest['dl']);
    $speedtest['ul'] = format($speedtest['ul']);
    $speedtest['ping'] = format($speedtest['ping']);
    $speedtest['jitter'] = format($speedtest['jitter']);
    $speedtest['timestamp'] = $speedtest['timestamp'];

    $ispinfo = json_decode($speedtest['ispinfo'], true)['processedString'];
    $dash = strpos($ispinfo, '-');
    if ($dash !== false) {
        $ispinfo = substr($ispinfo, $dash + 2);
        $par = strrpos($ispinfo, '(');
        if ($par !== false) {
            $ispinfo = substr($ispinfo, 0, $par);
        }
    } else {
        $ispinfo = '';
    }

    $speedtest['ispinfo'] = $ispinfo;

    return $speedtest;
}

/**
 * @param array $speedtest
 *
 * @return void
 */
function drawImage($speedtest)
{
    $data = formatSpeedtestDataForImage($speedtest);
    $dl = $data['dl'];
    $ul = $data['ul'];
    $ping = $data['ping'];
    $jit = $data['jitter'];
    $ispinfo = $data['ispinfo'];
    $timestamp = $data['timestamp'];

    // Canvas
    $SCALE  = 1.5;
    $WIDTH  = 400 * $SCALE;
    $HEIGHT = 229 * $SCALE;
    $im = imagecreatetruecolor($WIDTH, $HEIGHT);

    // lazyway palette
    $C_BASE        = imagecolorallocate($im, 255, 255, 255);  // #FFFFFF
    $C_BRAND_BLUE  = imagecolorallocate($im, 18,  39,  158);  // #12279E
    $C_AMBER       = imagecolorallocate($im, 217, 130, 31);   // #D9821F
    $C_MUTED       = imagecolorallocate($im, 97,  101, 127);  // #61657F
    $C_LINE        = imagecolorallocate($im, 229, 231, 241);  // #E5E7F1
    $C_SURFACE     = imagecolorallocate($im, 244, 245, 251);  // #F4F5FB

    // White background
    imagefilledrectangle($im, 0, 0, $WIDTH, $HEIGHT, $C_BASE);

    // Brand-blue top accent bar
    $BAR_H = (int)(5 * $SCALE);
    imagefilledrectangle($im, 0, 0, $WIDTH, $BAR_H, $C_BRAND_BLUE);

    // Surface tint behind ping/jitter zone
    $PING_ZONE_TOP = $BAR_H;
    $PING_ZONE_BOT = (int)(82 * $SCALE);
    imagefilledrectangle($im, 0, $PING_ZONE_TOP, $WIDTH, $PING_ZONE_BOT, $C_SURFACE);

    // Fonts
    $FONT_LABEL   = tryFont('IBMPlexSans-SemiBold');
    $FONT_METER   = tryFont('IBMPlexMono-SemiBold');
    $FONT_MEASURE = tryFont('IBMPlexMono-Regular');
    $FONT_SMALL   = tryFont('IBMPlexSans-Regular');

    // Font sizes
    $LABEL_SIZE     = 8  * $SCALE;
    $LABEL_SIZE_BIG = 10 * $SCALE;
    $METER_SIZE     = 19 * $SCALE;
    $METER_SIZE_BIG = 22 * $SCALE;
    $MEASURE_SIZE   = 9  * $SCALE;
    $ISP_SIZE       = 8  * $SCALE;
    $TSTAMP_SIZE    = 7  * $SCALE;
    $WMRK_SIZE      = 7  * $SCALE;

    // Text constants
    $MS_TEXT        = 'ms';
    $MBPS_TEXT      = 'Mbit/s';
    $PING_TEXT      = 'PING';
    $JIT_TEXT       = 'JITTER';
    $DL_TEXT        = 'DOWNLOAD';
    $UL_TEXT        = 'UPLOAD';
    $WATERMARK_TEXT = 'LibreSpeed';

    // Small gap between value and unit
    $GAP = 6 * $SCALE;

    // Column X centres
    $X_LEFT  = 120 * $SCALE;
    $X_RIGHT = 280 * $SCALE;

    // Ping/Jitter Y positions (measured from baseline)
    $Y_PING_LABEL   = (int)(28 * $SCALE);
    $Y_PING_METER   = (int)(63 * $SCALE);

    // Separator between zones
    $Y_SEP1 = (int)(84 * $SCALE);

    // Download/Upload Y positions
    $Y_DL_LABEL   = (int)(106 * $SCALE);
    $Y_DL_METER   = (int)(150 * $SCALE);
    $Y_DL_MEASURE = (int)(170 * $SCALE);

    // Footer separator
    $Y_SEP2 = (int)(188 * $SCALE);

    // ISP / timestamp / watermark
    $Y_ISP       = (int)(204 * $SCALE);
    $Y_FOOTER    = (int)(222 * $SCALE);

    // Pre-compute bounding boxes for centering
    $msBbox        = imageftbbox($MEASURE_SIZE,   0, $FONT_MEASURE, $MS_TEXT);
    $mbpsBbox      = imageftbbox($MEASURE_SIZE,   0, $FONT_MEASURE, $MBPS_TEXT);
    $pingBbox      = imageftbbox($LABEL_SIZE,     0, $FONT_LABEL,   $PING_TEXT);
    $pingMtrBbox   = imageftbbox($METER_SIZE,     0, $FONT_METER,   $ping);
    $jitBbox       = imageftbbox($LABEL_SIZE,     0, $FONT_LABEL,   $JIT_TEXT);
    $jitMtrBbox    = imageftbbox($METER_SIZE,     0, $FONT_METER,   $jit);
    $dlBbox        = imageftbbox($LABEL_SIZE_BIG, 0, $FONT_LABEL,   $DL_TEXT);
    $dlMtrBbox     = imageftbbox($METER_SIZE_BIG, 0, $FONT_METER,   $dl);
    $ulBbox        = imageftbbox($LABEL_SIZE_BIG, 0, $FONT_LABEL,   $UL_TEXT);
    $ulMtrBbox     = imageftbbox($METER_SIZE_BIG, 0, $FONT_METER,   $ul);
    $wmrkBbox      = imageftbbox($WMRK_SIZE,      0, $FONT_SMALL,   $WATERMARK_TEXT);
    $X_WATERMARK   = (int)($WIDTH - $wmrkBbox[4] - 4 * $SCALE);

    // --- PING ---
    imagefttext($im, $LABEL_SIZE, 0,
        (int)($X_LEFT - $pingBbox[4] / 2), $Y_PING_LABEL,
        $C_MUTED, $FONT_LABEL, $PING_TEXT);
    $pingValX = (int)($X_LEFT - ($pingMtrBbox[4] + $GAP + $msBbox[4]) / 2);
    imagefttext($im, $METER_SIZE, 0,
        $pingValX, $Y_PING_METER,
        $C_AMBER, $FONT_METER, $ping);
    imagefttext($im, $MEASURE_SIZE, 0,
        $pingValX + $pingMtrBbox[4] + $GAP, $Y_PING_METER,
        $C_MUTED, $FONT_MEASURE, $MS_TEXT);

    // --- JITTER ---
    imagefttext($im, $LABEL_SIZE, 0,
        (int)($X_RIGHT - $jitBbox[4] / 2), $Y_PING_LABEL,
        $C_MUTED, $FONT_LABEL, $JIT_TEXT);
    $jitValX = (int)($X_RIGHT - ($jitMtrBbox[4] + $GAP + $msBbox[4]) / 2);
    imagefttext($im, $METER_SIZE, 0,
        $jitValX, $Y_PING_METER,
        $C_AMBER, $FONT_METER, $jit);
    imagefttext($im, $MEASURE_SIZE, 0,
        $jitValX + $jitMtrBbox[4] + $GAP, $Y_PING_METER,
        $C_MUTED, $FONT_MEASURE, $MS_TEXT);

    // Zone separator
    imagefilledrectangle($im, 0, $Y_SEP1, $WIDTH, $Y_SEP1, $C_LINE);

    // --- DOWNLOAD ---
    imagefttext($im, $LABEL_SIZE_BIG, 0,
        (int)($X_LEFT - $dlBbox[4] / 2), $Y_DL_LABEL,
        $C_MUTED, $FONT_LABEL, $DL_TEXT);
    imagefttext($im, $METER_SIZE_BIG, 0,
        (int)($X_LEFT - $dlMtrBbox[4] / 2), $Y_DL_METER,
        $C_BRAND_BLUE, $FONT_METER, $dl);
    imagefttext($im, $MEASURE_SIZE, 0,
        (int)($X_LEFT - $mbpsBbox[4] / 2), $Y_DL_MEASURE,
        $C_MUTED, $FONT_MEASURE, $MBPS_TEXT);

    // --- UPLOAD ---
    imagefttext($im, $LABEL_SIZE_BIG, 0,
        (int)($X_RIGHT - $ulBbox[4] / 2), $Y_DL_LABEL,
        $C_MUTED, $FONT_LABEL, $UL_TEXT);
    imagefttext($im, $METER_SIZE_BIG, 0,
        (int)($X_RIGHT - $ulMtrBbox[4] / 2), $Y_DL_METER,
        $C_BRAND_BLUE, $FONT_METER, $ul);
    imagefttext($im, $MEASURE_SIZE, 0,
        (int)($X_RIGHT - $mbpsBbox[4] / 2), $Y_DL_MEASURE,
        $C_MUTED, $FONT_MEASURE, $MBPS_TEXT);

    // Footer separator
    imagefilledrectangle($im, 0, $Y_SEP2, $WIDTH, $Y_SEP2, $C_LINE);

    // ISP info
    if ($ispinfo) {
        imagefttext($im, $ISP_SIZE, 0,
            (int)(4 * $SCALE), $Y_ISP,
            $C_MUTED, $FONT_SMALL, $ispinfo);
    }

    // Timestamp (left) and watermark (right)
    imagefttext($im, $TSTAMP_SIZE, 0,
        (int)(4 * $SCALE), $Y_FOOTER,
        $C_MUTED, $FONT_SMALL, $timestamp);
    imagefttext($im, $WMRK_SIZE, 0,
        $X_WATERMARK, $Y_FOOTER,
        $C_MUTED, $FONT_SMALL, $WATERMARK_TEXT);

    header('Content-Type: image/png');
    imagepng($im);
}

$speedtest = getSpeedtestUserById($_GET['id']);
if (!is_array($speedtest)) {
    exit(1);
}

drawImage($speedtest);
