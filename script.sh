#!/bin/bash
(crontab -l | grep -v "/usr/bin/php /home/dokanda1/admin.dokandar.online/artisan dm:disbursement") | crontab -
(crontab -l ; echo "57 13 * * * /usr/bin/php /home/dokanda1/admin.dokandar.online/artisan dm:disbursement") | crontab -
(crontab -l | grep -v "/usr/bin/php /home/dokanda1/admin.dokandar.online/artisan store:disbursement") | crontab -
(crontab -l ; echo "57 13 * * * /usr/bin/php /home/dokanda1/admin.dokandar.online/artisan store:disbursement") | crontab -
(crontab -l | grep -v "/usr/bin/php /home/dokanda1/admin.dokandar.online/artisan redeem:package") | crontab -
(crontab -l ; echo "0 0 * * * /usr/bin/php /home/dokanda1/admin.dokandar.online/artisan redeem:package") | crontab -
