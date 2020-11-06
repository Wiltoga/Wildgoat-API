<?php

class EDT
{
    public $weeks = array();
    public $success = true;
}

class Week
{
    public function __construct()
    {
        $this->days = array(
            new Day,
            new Day,
            new Day,
            new Day,
            new Day,
            new Day,
            new Day
        );
    }
    public $days;
}

class Day
{
    public $events = array();
}


function ical_to_json($url, $weeks, $offset)
{
    $edt = new EDT;
    $raw = file_get_contents("https://ical-to-json.herokuapp.com/convert.json?url=" . urlencode($url));
    if ($raw != false) {
        $rawevents = json_decode($raw)->vcalendar[0]->vevent;
        for ($i = 0; $i < $weeks; $i++) {
            $edt->weeks[] = new Week;
        }
        foreach ($rawevents as $event) {
            $rawtime = substr($event->dtstart[0], 0, strlen('YYYYmmdd'));
            for ($i = $offset; $i < $offset + $weeks; $i++) {
                $j = $i - 1;
                $currMon = date('Ymd', strtotime("monday $j week"));
                if ($currMon <= $rawtime &&  $rawtime <= date('Ymd', strtotime("sunday $i week"))) {
                    $day_of_week = $rawtime - $currMon;
                    $edt->weeks[$i - $offset]->days[$day_of_week]->events[] = $event;
                }
            }
        }
    } else
        $edt->success = 'failed on getting distant JSON';

    return json_encode($edt);
}
