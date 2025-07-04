<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
defined('SECRET_KEY')      	   OR define('SECRET_KEY', '6LdH4U0UAAAAAHkrMtGVWoPswn6JQH-ckiosyWKA'); // highest automatically-assigned error code
defined('SUPER_ADMIN')          OR define('SUPER_ADMIN', 'SUPER ADMIN');
defined('ADMIN')         	   	OR define('ADMIN', 'ADMIN');
defined('SADMIN_ROLE')         	OR define('SADMIN_ROLE', 1);
defined('ADMIN_ROLE')         	OR define('ADMIN_ROLE', 2);
defined('BRANCH_HO')         	OR define('BRANCH_HO', 1);
defined('TOTAL_ROWS')          	OR define('TOTAL_ROWS', 20);
defined('PER_PAGE')            	OR define('PER_PAGE', 20);
defined('OFFSET')              	OR define('OFFSET', 0);
defined('PAYMENT')              OR define('PAYMENT', 'PAYMENT');
defined('RECEIPT')              OR define('RECEIPT', 'RECEIPT');
defined('TO_PAY')              	OR define('TO_PAY', 'TO PAY');
defined('TO_RECEIVE')          	OR define('TO_RECEIVE', 'TO RECEIVE');
defined('PURCHASE')            	OR define('PURCHASE', 'PURCHASE');
defined('SALES')           	 	OR define('SALES', 'SALES');
defined('DISPATCH')        	 	OR define('DISPATCH', 'DISPATCH');
defined('SOURCING')        	 	OR define('SOURCING', 'SOURCING');
defined('CUSTOMER')          	OR define('CUSTOMER', 'CUSTOMER');
defined('SUPPLIER')          	OR define('SUPPLIER', 'SUPPLIER');
defined('GENERAL')          	OR define('GENERAL', 'GENERAL');
defined('WITHIN')          		OR define('WITHIN', 'WITHIN');
defined('OUTSIDE')          	OR define('OUTSIDE', 'OUTSIDE');
defined('REFRESH')          	OR define('REFRESH', 'REFRESH');
defined('LAZYLOADING')          OR define('LAZYLOADING', 'dist/images/loading.webp');
defined('NOIMAGE')          	OR define('NOIMAGE', 'dist/images/no-image.jpg');
defined('USERIMAGE')          	OR define('USERIMAGE', 'dist/images/user-image.png');   
defined('QRCODE_TYPE')  	    OR define('QRCODE_TYPE', 'SMALL');
defined('MAX_QTY')  	        OR define('MAX_QTY', 300);
defined('COMPANY_INITIAL')  	OR define('COMPANY_INITIAL', 'REG');
defined('ENV')                  OR define('ENV', 'PROD');
defined('DEV')                  OR define('DEV', 'DEV');
defined('PROD')                 OR define('PROD', 'PROD');
defined('LIVE_DOMAIN')          OR define('LIVE_DOMAIN', 'https://rsupport.in/');
defined('LOCAL_DOMAIN')         OR define('LOCAL_DOMAIN', 'http://localhost/');
defined('BARCODE_LENGTH')       OR define('BARCODE_LENGTH', 12);
defined('BLANK_PDF')     		OR define('BLANK_PDF', 'http://interlinkapp.in/twix/public/uploads/sales/blank.pdf');

defined('API_ACCESS_KEY')       OR define('API_ACCESS_KEY', 'ZkC6BDUzxz'); // api access key
defined('VERSION')              OR define('VERSION', '1.0'); // version
defined('LIMIT')            	OR define('LIMIT', 10);
defined('OFFSET')              	OR define('OFFSET', 0);
defined('NEXT_OFFSET')          OR define('NEXT_OFFSET', 1);
