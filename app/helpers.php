<?php

if (!function_exists('rupiah_format')) {
    function rupiah_format($number)
    {
        return 'Rp. ' . number_format($number, 0, ',', '.');
    }
}
