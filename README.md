# sfi_member_util
SFI Member Verification API

Class & methods provided are compatible with PHP 5.3 and above.

Be sure not to place this script in the site's root directory; it should be in its own. Unless a different location is specified for the request log, make sure the directory is writeable by the web server.

I've made some assumptions with the database structure, please revise the SELECT query to fit.  I assume a few edits will be necessary to initialize the database connection.

If communicating the membership expiration date is allowing too much information, sending a true/false boolean will be fine.
