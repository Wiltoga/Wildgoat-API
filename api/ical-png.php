<?php
include_once("./includes/table-image.php");
include_once("./includes/ical-json.php");
include_once("./includes/utils.php");
if (isset($_GET['url']) and !empty($_GET['url'])) {
    $url = $_GET['url'];
    $offset = 0;
    $regex = "/.+/";
    if (isset($_GET['regex']) and !empty($_GET['regex']))
        $regex = $_GET['regex'];
    if (isset($_GET['offset']) and !empty($_GET['offset']))
        $offset = intval($_GET['offset']);
    $json = json_decode(ical_to_json($url, 1, $offset));
    if ($json->success) {
        $day_of_week = 0;
        $table = new table();
        $table->font_family_file = dirname(__FILE__) . "/fonts/roboto.ttf";
        $table->font_size = 10;
        $table->lines_color = [0, 0, 0];
        $table->background_color = [200, 200, 200];
        $table->display_columns = true;
        $table->display_rows = false;
        $table->min_cell_size = [5, 5];
        $table->cell_padding = 5;
        $table->set_cell(new cell(1, 1, [], [0, 0, 0], [255, 255, 255]), 0, 0);
        $min_hour = 24;
        $max_hour = 0;
        $monday = strtotime("monday " . ($offset - 1) . " week");
        foreach ($json->weeks[0]->days as $day)
            foreach ($day->events as $event) {
                $start_event = DateTime::createFromFormat('Ynj\THis', $event->dtstart[0])->getTimestamp();
                $end_event = DateTime::createFromFormat('Ynj\THis', $event->dtend[0])->getTimestamp();
                $day = intval($start_event / 3600 / 24) - intval($monday / 3600 / 24) - 1;
                $curr_start_hour = intval(round($start_event / 3600)) - intval($monday / 3600) - $day * 24;
                $curr_end_hour = intval(round($end_event / 3600)) - intval($monday / 3600) - $day * 24;
                if ($min_hour > $curr_start_hour)
                    $min_hour = $curr_start_hour;
                if ($max_hour < $curr_end_hour)
                    $max_hour = $curr_end_hour;
            }

        foreach ($json->weeks[0]->days as $day) {
            foreach ($day->events as $event) {
                $content = [];
                $matchs = [];
                $start_event = DateTime::createFromFormat('Ynj\THis', $event->dtstart[0])->getTimestamp();
                $end_event = DateTime::createFromFormat('Ynj\THis', $event->dtend[0])->getTimestamp();

                $day = intval($start_event / 3600 / 24) - intval($monday / 3600 / 24) - 1;
                $start_quarter = intval(round($start_event / 900.)) - intval($monday / 900) - $day * 96;
                $end_quarter = intval(round($end_event / 900.)) - intval($monday / 900) - $day * 96;
                $start_min = intval(($start_quarter / 4. - intval($start_quarter / 4)) * 60);
                if ($start_min < 10)
                    $start_min = "0" . $start_min;
                $end_min = intval(($end_quarter / 4. - intval($end_quarter / 4)) * 60);
                if ($end_min < 10)
                    $end_min = "0" . $end_min;
                $start_time = intval($start_quarter / 4) . ":" . $start_min;
                $end_time = intval($end_quarter / 4) . ":" . $end_min;
                $content[] = "$start_time - $end_time";
                $row_pos = ($start_quarter - $min_hour * 4) + 1;
                $row_span = $end_quarter - $start_quarter;
                preg_match_all("$regex", $event->summary, $matchs);
                if (count($matchs) == 1)
                    foreach ($matchs[0] as $match)
                        $content[] = $match;
                else if (count($matchs) > 1) {
                    for ($i = 0; $i < count($matchs[0]); $i++)
                        for ($j = 1; $j < count($matchs); $j++)
                            if ($matchs[$j][$i] != null && $matchs[$j][$i] != "")
                                $content[] = $matchs[$j][$i];
                }
                $content[] = $event->location;
                $table->set_cell(new cell(1, $row_span, $content, [0, 0, 0], hslToRgb([hashcode_string($content[1]) % 360, .8, .75])), $day + 1, $row_pos);
            }
            $day_disp;
            switch ($day_of_week) {
                case 0:
                    $day_disp = "Lundi";
                    break;
                case 1:
                    $day_disp = "Mardi";
                    break;
                case 2:
                    $day_disp = "Mercredi";
                    break;
                case 3:
                    $day_disp = "Jeudi";
                    break;
                case 4:
                    $day_disp = "Vendredi";
                    break;
                case 5:
                    $day_disp = "Samedi";
                    break;
                case 6:
                    $day_disp = "Dimanche";
                    break;
            }
            $date = date("Ymd", $monday + 3600 * 24 * $day_of_week);
            $table->set_cell(new cell(1, 1, [$day_disp, substr($date, 6) . '/' . substr($date, 4, 2)], [0, 60, 120], [255, 255, 255]), $day_of_week + 1, 0);
            $day_of_week++;
        }
        for ($i = $min_hour; $i < $max_hour; $i++)
            if ($i < 10)
                $table->set_cell(new cell(1, 4, ["0$i:00"], [160, 0, 60], [255, 255, 255]), 0, 1 + ($i - $min_hour) * 4);
            else
                $table->set_cell(new cell(1, 4, ["$i:00"], [160, 0, 60], [255, 255, 255]), 0, 1 + ($i - $min_hour) * 4);
        $path = "generated/" . uniqid(rand(), true) . '.png';
        imagepng($table->generate_image(), $path);
        header("Location: $path", true, 307);
        die();
    } else
        echo $json->succuess;
} else {
    echo 'no url provided';
}
