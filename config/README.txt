OpenTTD Server Configuration System
by Olexandr Savchuk

Free to use, modify and distribute under the GNU General Public License Version 3 (see also LICENSE.txt).

A simple configuration editor system for OpenTTD.

Installation: 

1) Unpack the archive somewhere where you can access the .php files with your webserver. 
2) Edit config.php with paths to your openttd.cfg, as well as content download system data dir and OpenTTD installation data dir.
3) Make sure that webserver user (usually www-data) has read and write (rw) access to the openttd.cfg file, and read and list (rx) access to both data folders and all files in them.

The system doesn't offer any protection from misuse by anyone, so be sure to protect the directory on the webserver, for example with a .htaccess.

The author is not responsible for any changes to your server configuration or anything breaking, the system is provided as-is with no guarantees.

Have fun!
