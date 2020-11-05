<?php
header('Content-Type: application/json');

class EDT
{
    public $weeks = array();
    public $success = true;
}

class Week
{
    public $days = array();
}

class Day
{
    public $events = array();
}

$edt = new EDT;

if (isset($_GET['url']) and !empty($_GET['url'])) {
    $url = $_GET['url'];
    $weeks = 2;
    $events = array();
    $offset = 0;
    if (isset($_GET['weeks']) and !empty($_GET['weeks']))
        $weeks = intval($_GET['weeks']);
    if (isset($_GET['offset']) and !empty($_GET['offset']))
        $offset = intval($_GET['offset']);
    $raw = file_get_contents("https://ical-to-json.herokuapp.com/convert.json?url=$url");
    if ($raw != false) {
        $rawevents = json_decode($raw)->vcalendar[0]->vevent;
        for ($i = 0; $i < $weeks; $i++) {
            $edt->weeks[] = new Week;
        }
        $lastDate = "";
        foreach ($rawevents as $event) {
            $rawtime = substr($event->dtstart[0], 0, strlen('YYYYmmdd'));
            for ($i = $offset; $i < $offset + $weeks; $i++) {
                $j = $i - 1;
                $currMon = date('Ymd', strtotime("monday $j week"));
                if ($currMon <= $rawtime &&  $rawtime <= date('Ymd', strtotime("saturday $i week"))) {
                    if (strcmp($lastDate, $rawtime) != 0) {
                        $lastDate = $rawtime;
                        $edt->weeks[$i]->days[] = new Day;
                    }
                    end($edt->weeks[$i]->days)->events[] = $event;
                }
            }
        }
    } else
        $edt->success = 'failed on getting distant JSON';
} else
    $edt->success = 'no url provided';

echo json_encode($edt);
