<?php

namespace app\helpers;

class Formatters
{
    function humanElapse($from, $to, $full=false)
    {
        $i = [];
        $a = $from->diff($to);
        if($a->invert) return '0 days';

        if($a->y) $i[]=self::prettyTime('Year',   $a->y);
        if($a->m) $i[]=self::prettyTime('Month',  $a->m);
        if($a->d) $i[]=self::prettyTime('Day',    $a->d);
        if($a->h) $i[]=self::prettyTime('Hour',   $a->h);
        if($a->i) $i[]=self::prettyTime('Minute', $a->i);
        if($a->s) $i[]=self::prettyTime('Second', $a->s);

        return $full?implode($i,'&nbsp;'):@$i[0];

    }

    function humanElapseDays($from, $to){
        $i = 0;
        $a = $from->diff($to);
        if($a->invert) return '0 days';
        return self::prettyTime('Day', $a->days);
    }

    function prettyTime($type,$count,$nbsp=true){
        return $count.($nbsp?'&nbsp;':' ').$type.($count>1||$count==0?'s':'');
    }
}