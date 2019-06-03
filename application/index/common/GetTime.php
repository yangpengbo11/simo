<?php


namespace app\index\common;


class GetTime
{
    public  function ts_time($format = 'u', $utimestamp = null)
    {
        if (is_null($utimestamp)) {
            $utimestamp = microtime(true);
        }

        $timestamp = floor($utimestamp);
        $milliseconds = round(($utimestamp - $timestamp) * 1000);

        return date("ymdHis").date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);

    }
}