*Note: This project is a work in progress. Major stability and security issues ahead!*

![last-commit](https://img.shields.io/github/last-commit/andrei-ccr/cloud-fileman)

## Installing

Server must run PHP7+ and MySQL.

The files in `/sys/` contain the platform functionality code. This is required for the platform to work. Must be placed on the server, preferably in the root directory (otherwise, paths need to be changed in the default UI, if it's used)

The rest of the files are the platform's default UI. They can be placed on the same server as the system's code or on another server depending on the intended setup (requires paths to be changed properly). They can also be replaced by another UI. If the UI is not on web then no other files are required.

Files that are not essential and can be deleted:
- `/preview/`
- `/installdb.php`
- `/temp_disc_cleanup_event.sql`
- `/README.md`

Some additional configuration is required for the platform to work properly.


## Required configuration
- Enable `mod_rewrite` on the server and make sure .htaccess files are working
- Update database credentials in `/sys/obj/Connection.php` to match server's configuration. Create the database `cloud_disc` THEN import the database structure using `installdb.php`

- Make sure PHP and MySQL time matches (timezones differ in some installations)

## Preview

### Default interface
![Default interface](/preview/1.png)

