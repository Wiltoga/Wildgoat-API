# ical-png

Usage

`http://wildgoat.fr/api/ical-png.php?`

Displays the calendar of the current week.

`GET` Parameters :

 - `url=` : **Required**, encoded url. Indicates which Ical to target.
 - `offset=` : Default 0. The offset of the week. ex : 1 for next week, -1 for previous week.
 + `regex=` : Default `/.+/`, encoded regex. The regular expression used to find the informations in the `summary` property of an ical. Will try to catch the groups found, or the matched strings if no group is given. If the regex doesn't find any information, it won't display anything.