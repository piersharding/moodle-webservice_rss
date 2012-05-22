 local/rsscourse
 ==================
 
 RSS generator functions for course lists that work with webservice/rss (https://gitorious.org/moodle-webservice_rss/moodle-webservice_rss)
 
 A simple test mechanism is:
 
 GET -UE -H 'Host: moodle.local.net' 'http://moodle.local.net/moodledev/webservice/rss/server.php?wstoken=0676584db99cd7ff70721b48b32a7abc&id=49&wsrssformat=rss91&wsfunction=local_rsscourse_get_courses'
 