<?php

include_once("utils.php");
include_once("CalFileParser.php");
class EDT
{
    public $weeks = array();
    public $success = true;
    public $code = -1;
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

function date_to_universal($date)
{
    $out = substr($date, 0, 4);
    $out .= substr($date, 5, 2);
    $out .= substr($date, 8, 2);
    $out .= 'T';
    $out .= substr($date, 11, 2);
    $out .= substr($date, 14, 2);
    $out .= substr($date, 17, 2);
    return $out;
}

function ical_to_json($url, $weeks, $offset)
{
    $edt = new EDT;
    $cal = new CalFileParser();
    $cal->set_timezone('Europe/Berlin');
    $raw = $cal->parse($_GET['url'], 'json');
    if ($raw != false) {
        $rawevents = json_decode($raw);
        for ($i = 0; $i < $weeks; $i++) {
            $edt->weeks[] = new Week;
        }
        $today_monday = date('Ymd', strtotime("now")) == date('Ymd', strtotime("monday 0 week"));
        foreach ($rawevents as $event) {
            $rawtime = substr(date_to_universal($event->DTSTART->date), 0, strlen('YYYYmmdd'));
            for ($i = $offset; $i < $offset + $weeks; $i++) {
                $j = $i - 1;
                if ($today_monday)
                    $j = $i;
                else
                    $j = $i - 1;
                $currMon = date('Ymd', strtotime("monday $j week"));
                if ($currMon <= $rawtime && $rawtime <= date('Ymd', strtotime("sunday $i week"))) {
                    $edt->code += hashcode_string(date_to_universal($event->DTSTART->date));
                    $edt->code += hashcode_string(date_to_universal($event->DTEND->date));
                    $edt->code += hashcode_string($event->SUMMARY);
                    $day_of_week = $rawtime - $currMon;
                    $edt->weeks[$i - $offset]->days[$day_of_week]->events[] = new class ($event)
                    {
                        public function __construct($event)
                        {
                            $this->dtstart = date_to_universal($event->DTSTART->date);
                            $this->dtend = date_to_universal($event->DTEND->date);
                            $this->summary = $event->SUMMARY;
                            $this->location = $event->LOCATION;
                        }
                        public $dtstart;
                        public $dtend;
                        public $summary;
                        public $location;
                    };
                }
            }
        }
    } else
        $edt->success = 'failed on getting distant JSON';

    return json_encode($edt);
}
