#!/bin/bash
(crontab -l | grep -v "/usr/bin/php /home/956826.cloudwaysapps.com/pcenmpwtgr/public_html/artisan dm:disbursement") | crontab -
(crontab -l ; echo "57 13 * * * /usr/bin/php /home/956826.cloudwaysapps.com/pcenmpwtgr/public_html/artisan dm:disbursement") | crontab -
(crontab -l | grep -v "/usr/bin/php /home/956826.cloudwaysapps.com/pcenmpwtgr/public_html/artisan store:disbursement") | crontab -
(crontab -l ; echo "57 13 * * * /usr/bin/php /home/956826.cloudwaysapps.com/pcenmpwtgr/public_html/artisan store:disbursement") | crontab -
