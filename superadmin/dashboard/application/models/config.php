<?php

/*
 * App specific details
 */
define("APP_NAME", "TaxiParent"); // Will be used for all push notifications, mails, invoices and any other places where app name is needed to display
define("APP_DISTANCE_METERS", "1609.08"); // Meters
define("APP_SERVER_HOST", "http://www.ideliver.mobi/Taxi/");
define("APP_PIC_HOST", "http://www.ideliver.mobi/Taxi/pics/");
define("APP_PUBNUB_CHANNEL", "taxi_parent");

/*
 * Payment related things
 */
/*
 * ENABLED -    Will charge the user 2.9 percent + 30 cents extra from the card, while doing the transaction
 *              These are the fees that stripe will take from the transaction amount
 *              If you want to take these charges in the app commission itself leave it DISABLED, if you want to take from the user make it ENABLED
 * DISABLED -   DISABLE the extra charge levied on customer for the card transaction 
 */
define("PAYMENT_CC_FEES", "DISABLED");

/*
 * APP Commission in percentage
 * This is the percentage of amount that you take in every booking
 * The driver earnings are calculated according to this 
 * eg: Total amount = 100.00 Units
 *     App Comm = 10.00 (10% this case)
 *     PG Comm = 2.93 (2.9% + 30 Cents) - STRIPE / BRAINTREE (Only for card payments)
 *     Driver earnings = 87.07 (Card transaction)
 *                       90.00 (Cash transaction)
 */

define("PAYMENT_APP_COMMISSION", 10);

/*
 *  CURRENCY to perform transactions, codes present in the below link are supported
 *  https://support.stripe.com/questions/which-currencies-does-stripe-support
 */

define("PAYMENT_BASE_CURRENCY", "USD");

/*
 * -----------------------------------------------------------------------------
 */
/*
 * Android push api keys
 */

define("ANDROID_DRIVER_PUSH_KEY", "AIzaSyBaC54G6DMFz0peLg6oLDK7gkg7L6RwT70");
define("ANDROID_PASSENGER_PUSH_KEY", "AIzaSyDLRakgURXhTp_g5vbKOZ842cvhBgNmBEY");

/*
 * -----------------------------------------------------------------------------
 */
/*
 * IOS push cert absolute paths or aws keys
 */

/*
 *  The type of push that should be used in the whole application
 *  Values cab be one of the following
 *  APPLE - (Less efficient) Uses apple native push using pem and password
 *  AMAZON - (More efficient) Uses amazon aws SNS push service, you must have applications created in aws SNS service, for help: https://aws.amazon.com/sns/

 *  PUSH ENVIRONMENT
 *  sandbox - Development or debug mode
 *  production - Distribution or production or live mode
 */

define("IOS_PUSH_TYPE", "AMAZON");

define("IOS_PUSH_ENVIRONMENT", "production");

/*
 *  IF AMAZON is given in the above constant please fill the below details, else remain as is.
 */

/*
 * --------------------------------------------
 *  These details you will get to see if you create applications in the aws sns panel, application arn is a unique string for each application
 */
define("AMAZON_DRIVER_APPLICATION_ARN", "arn:aws:sns:us-west-2:797797333700:app/APNS/Taxi-Driver");
define("AMAZON_PASSENGER_APPLICATION_ARN", "arn:aws:sns:us-west-2:797797333700:app/APNS/Taxi-Pax");

/*
 *  AWS access key, secret and region. For help: http://docs.aws.amazon.com/general/latest/gr/aws-security-credentials.html
 *  These keys can be for root user or a "I AM" user, who must have given access to sns service
 *  Region is the one which you created the applications. For help: http://docs.aws.amazon.com/general/latest/gr/rande.html  
 */

define("AMAZON_AWS_ACCESS_KEY", "AKIAIEQ4NVTU73TPVHXA");
define("AMAZON_AWS_AUTH_SECRET", "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");
define("AMAZON_AWS_SNS_REGION", "us-west-2");

/*
 * --------------------------------------
 *  iOS push certificate path and password for driver and passenger, leave password empty if not given while creating the pem certificate
 */
define("IOS_DRIVER_PEM_PATH", "/var/www/html/Taxi/cert/PocketP.pem");
define("IOS_DRIVER_PEM_PASS", "3embed");

define("IOS_PASSENGER_PEM_PATH", "/var/www/html/Taxi/cert/pocketCabs_push_cert.pem");
define("IOS_PASSENGER_PEM_PASS", "3embed");

/*
 * -----------------------------------------------------------------------------
 */
/*
 * Mysql specific details
 */
define("MYSQL_HOST", "localhost"); // eg: localhost / xxx.xxx.xxx.xxx
define("MYSQL_USER", "root"); // eg: admin_user
define("MYSQL_PASS", "u5B5&fJAwjw?fwZ"); // eg: BrBW5M99Xh!g^D@v
define("MYSQL_DB", "taxi_parent"); // eg: taxi_db

/*
 * Mongodb specific details
 */
define("MONGODB_HOST", "localhost"); // eg: localhost / xxx.xxx.xxx.xxx
define("MONGODB_USER", "taxi_parent"); // eg: admin_user
define("MONGODB_PASS", "r%BN%zJmNXwQ?R8_"); // eg: BrBW5M99Xh!g^D@v
define("MONGODB_DB", "taxi_parent"); // eg: taxi_db
define("MONGODB_PORT", "27017"); // eg: 27017
/*
 * Mandrill api key
 *  Login/signup to the mandrill account at http://mandrillapp.com/
 *  Move on to settings, create an api key, paste it below here.
 *  yay, its that easy
 * Email id from which the user will see mails from.
 * Domain from which user will see the email from.  
 */
define("MANDRILL_API_KEY", "dBQ1sypJtJdKsfSKALPNlA");

define("MANDRILL_FROM_NAME", "3Embed"); // eg: Support
define("MANDRILL_FROM_EMAIL", "info@3embed.com"); // eg: info@domain.com
define("MANDRILL_FROM_WEBSITE", "www.3embed.com"); // eg: www.domain.com

/*
 *  Pubnub account keys, publish and subscribe keys
 */

define("PUBNUB_PUBLISH_KEY", "pub-c-ee59d111-d086-47a6-96f3-5fd1f41c3105"); // eg: pub-c-xxxxxx-xxx-xxxxx-xxxxxxxxxx
define("PUBNUB_SUBSCRIBE_KEY", "sub-c-67408fb4-404c-11e5-81f8-02ee2ddab7fe"); // eg: sub-c-xxxxx-xxx-xxxxx-xxxxxxxxxx

/*
 *  Stripe api secret key
 *  If test secret key is used then the app must use the test publish key and same for live keys
 *  If you want to change the keys, you must remove all the stripe ids from slave table of mysql db (Old users will not have their cards, will have to add new cards)
 */

define("STRIPE_API_SECRET_KEY", "sk_test_ugSQUJAwnHOnn0AukcvkBQyI"); // eg: sk_test_xxxxxxxxxxxxxxxxxxx
