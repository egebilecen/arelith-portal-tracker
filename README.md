# Arelith Portal Tracker
A panel for tracking players/characters activity, playtime etc. All data is parsed from [Arelith Portal](http://portal.arelith.com/) via a cronjob script.
`portal_util.php` holds some customizable options at the beginning of the file. MySQL DB structure can be imported from `arelith_portal_tracker.sql` file.

Contents of the `dashboard` folder should be copied to `/home/username/public_html/`. `cronjob`, `libs` and `config.php`, `portal_util.php` folders and files should be located in `/home/username/`. Don't forget to change the `PORTAL_UPDATE_INTERVAL`'s value defined in `portal_util.php` to the time interval you set in cronjob. For example, if you are running the cronjob every 5 minutes, set `PORTAL_UPDATE_INTERVAL` define value to `5`. And update the `$MAIN_PATH` variable in `cronjob/fetcher.php` to your home path.

# Screenshots
Dashboard:
![Screenshot_2](https://user-images.githubusercontent.com/29331682/144101275-09eabde6-0152-4ad1-8088-8c7305c30494.png)
![Screenshot_5](https://user-images.githubusercontent.com/29331682/144105640-7d0ea612-890e-41d4-872a-452017065b70.png)

Search:
![Screenshot_3](https://user-images.githubusercontent.com/29331682/144106600-5f5690a7-08f1-4a40-a543-35a6c0050014.png)



Character Page:
![Screenshot_3](https://user-images.githubusercontent.com/29331682/144101300-e16a94e0-99ea-41ab-a8a9-561382680379.png)
![Screenshot_4](https://user-images.githubusercontent.com/29331682/144101310-2e850f87-20bd-4e1b-878f-a2591e98b3a4.png)
![Screenshot_7](https://user-images.githubusercontent.com/29331682/144106194-b1837807-ffed-4faa-9ac4-6dc7ad5651cc.png)

Player Page:
![Screenshot_6](https://user-images.githubusercontent.com/29331682/144101630-269a3366-c0fd-4354-bd80-58a243f38978.png)
![Screenshot_8](https://user-images.githubusercontent.com/29331682/144106253-e99dd793-7860-48a5-8cf8-c848dae4c1ea.png)

