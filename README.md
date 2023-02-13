# SSLCommerz-installation

[NOTE] :  [IF FOLDER ALREADY EXIST THEN DONT NEED TO CREATE ANY FOLDER] 
	        [ADMIN/BACKEND SERVE PORT MUST BE 8000]

SETP 1 => Copy the "Library/SslCommerz" folder and put it in the laravel project's "App/" directory. If needed, then run composer dump -o.

STEP 2 => Copy the "config/sslcommerz.php" file into your project's "config/" folder.

	        [NOTE] : If you later encounter issues with session destroying after redirect, you can set 'same_site' => 'none' in your `config/session.php` file.

SETP 3 => Copy STORE_ID and STORE_PASSWORD form ".env" file to your ".env" file.

STEP 4 => Copy "Middleware/VerifyCsrfToken" middleware and paste it to the Directory is "App/Http/Middleware/"
	  
[ * STEP 4 ALTERNATE * ] Or you can just pase the exceptions on this directory "App/Http/Middleware/VerifyCsrfToken"

	        protected $except = [
    		      '/success',
    		      '/cancel',
    		      '/fail',
    		      '/ipn'
    		      '/pay-via-ajax', // only required to run example codes. Please see bellow.
	        ];

STEP 5 => Copy "Controllers/PaymentMethod" folder into your project's "App/Http/Controllers/" folder.

STEP 6 => Copy the routes from "routes/web.php" file and paste it on your "routes/web.php".

STEP 7 => Copy the blade file from "views" folder and paste it on your "resources/views/" directory.
