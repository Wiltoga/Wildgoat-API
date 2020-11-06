# ical-json

Usage

`http://wildgoat.fr/api/ical-json.php?`

Converts an ical into a JSON starting from the current week.

`GET` Parameters :

 - `url=` : **Required**, encoded url. Indicates which Ical to target.
 - `weeks=` : Default 2. Number of weeks to get.
 - `offset=` : Default 0. The offset of the week. ex : 1 for next week, -1 for previous week.
