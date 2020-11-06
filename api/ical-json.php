<?php

include_once("./includes/ical-json.php");

header('Content-Type: application/json');

if (isset($_GET['url']) and !empty($_GET['url'])) {
    $url = $_GET['url'];
    $weeks = 2;
    $offset = 0;
    if (isset($_GET['weeks']) and !empty($_GET['weeks']))
        $weeks = intval($_GET['weeks']);
    if (isset($_GET['offset']) and !empty($_GET['offset']))
        $offset = intval($_GET['offset']);
    echo ical_to_json($url, $weeks, $offset);
} else
    echo json_encode(new class ()
    {
        public $weeks = [];
        public $success = 'no url provided';
    });