<?php

function format_rupiah($price)
{
    $rupiah = "Rp" . number_format($price, 0, '', '.');
    return $rupiah;
}

function numhash($n)
{
    return ((0x0000000F & $n) << 4) + ((0x000000F0 & $n) >> 4)
        + ((0x00000F00 & $n) << 4) + ((0x0000F000 & $n) >> 4)
        + ((0x000F0000 & $n) << 4) + ((0x00F00000 & $n) >> 4)
        + ((0x0F000000 & $n) << 4) + ((0xF0000000 & $n) >> 4);
}

function unset_all(&$data, array $values)
{
    foreach ($values as $value) {
        if (is_object($data)) {
            unset($data->$value);
        } else {
            unset($data[$value]);
        }
    }
    return $data;
}

function curl_get_file_contents($URL)
{
    $c = curl_init();
    curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($c, CURLOPT_URL, $URL);
    $contents = curl_exec($c);
    curl_close($c);

    if ($contents) return $contents;
    else return FALSE;
}

function calcAverageRating($ratings)
{
    $totalWeight = 0;
    $totalReviews = 0;

    foreach ($ratings as $weight => $numberofReviews) {
        $WeightMultipliedByNumber = $weight * $numberofReviews;
        $totalWeight += $WeightMultipliedByNumber;
        $totalReviews += $numberofReviews;
    }

    $averageRating = $totalWeight === 0 ? 0 : ($totalWeight / $totalReviews);

    return $averageRating;
}


function isValid($valid, $expired)
{
    $day            = strtotime(date('Y-m-d H:i:s'));
    $valid_date     = strtotime($valid);
    $expiry_date    = strtotime($expired);
    return $valid_date <= $day && $expiry_date > $day;
}

function isKey($key, $array)
{
    for ($i = 0; $i < count($array); $i++) {
        if ($array[$i] === $key) {
            return true;
        }
    }
    return false;
}

function getSlug($text)
{ 
  $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
  $text = trim($text, '-');
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  $text = strtolower($text);
  $text = preg_replace('~[^-\w]+~', '', $text);
  if (empty($text))
  {
    return 'n-a';
  }
  return $text;
}
