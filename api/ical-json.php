<?php

include_once("./includes/ical-json.php");
include_once("./includes/CalFileParser.php");

header('Content-Type: application/json');

if (isset($_GET['url']) and !empty($_GET['url'])) {
    $url = $_GET['url'];
    $weeks = 2;
    $offset = 0;
    $timezone = 'Europe/Berlin';
    if (isset($_GET['weeks']) and !empty($_GET['weeks']))
        $weeks = intval($_GET['weeks']);
    if (isset($_GET['timezone']) and !empty($_GET['timezone']))
        $timezone = intval($_GET['timezone']);
    if (isset($_GET['offset']) and !empty($_GET['offset']))
        $offset = intval($_GET['offset']);
    echo ical_to_json($url, $weeks, $offset, $timezone);
} else
    echo json_encode(new class ()
    {
        public $weeks = [];
        public $success = 'no url provided';
    });
