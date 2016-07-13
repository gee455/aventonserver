<?php

/*
 * App specific details
 */
define("APP_NAME", "Roadyo"); // Will be used for all push notifications, mails, invoices and any other places where app name is needed to display
define("APP_DISTANCE_METERS", "1609.34"); // miles, for KM, 1000
define("APP_DISTANCE_METRIC", "Miles"); // String, Miles / KM
define("APP_SERVER_HOST", "http://aventonserver.herokuapp.com/");
define("APP_PIC_HOST", "http://aventonserver.herokuapp.com/pics/");
define("APP_PUBNUB_CHANNEL", "channelName");
define("APP_DRIVER_INACTIVATE_TIME", 300); // seconds for the driver to get inactivated, when there is no update of location from driver application
define("CURRENCY_SYMBOLE", "DA"); // seconds for the driver to get inactivated, when there is no update of location from driver application

define("REFERRAL_CODE_EXPIRY_MONTHS", 120); // number of months a user can be able to share his referral code and get promo codes for each signup

define("Ios_ClientmapKey", "");  // map key for ios pessanger app  
define("And_ClientmapKey", "");  // map key  for android passenger app

define("And_ClientPlaceKey", "");  // map key  for android passenger app
define("Ios_ClientPlaceKey", "");  // map key  for android passenger app

                
                
define("Ios_MastermapKey", "");  // map key  for ios driver
define("And_MastermapKey", "");  // map key  for  android  driver

define("presenseChn", "presenceChn_channel"); // 

define("stipeKeyForApp", "pk_live_XXXXXXXXXX"); // client stripe key 


define("ALLOW_DRIVER_UPTO", 5); 
define("PROMOCODE_CODE_EXPIRY_MONTHS", 30); // number of days a user can be able to use his promo code sent via email

define("APP_DRIVER_INACTIVATE_TIME",900);
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

define("ANDROID_DRIVER_PUSH_KEY", "");
define("ANDROID_PASSENGER_PUSH_KEY", "");

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
 *  PUSHWOOSH - (More efficient) Uses Third party service called pushwoosh for sending push notifications

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
define("AMAZON_DRIVER_APPLICATION_ARN", "");
define("AMAZON_PASSENGER_APPLICATION_ARN", "");

/*
 *  AWS access key, secret and region. For help: http://docs.aws.amazon.com/general/latest/gr/aws-security-credentials.html
 *  These keys can be for root user or a "I AM" user, who must have given access to sns service
 *  Region is the one which you created the applications. For help: http://docs.aws.amazon.com/general/latest/gr/rande.html  
 */

define("AMAZON_AWS_SNS_REGION", "ap-southeast-1");

define("AMAZON_AWS_ACCESS_KEY", "");
define("AMAZON_AWS_AUTH_SECRET", "");

/*
 * --------------------------------------
 *  iOS push certificate path and password for driver and passenger, leave password empty if not given while creating the pem certificate
 */
define("IOS_DRIVER_PEM_PATH", "");
define("IOS_DRIVER_PEM_PASS", "");

define("IOS_PASSENGER_PEM_PATH", "");
define("IOS_PASSENGER_PEM_PASS", "");


/* For development certificate use --> ssl://gateway.sandbox.push.apple.com:2195 
 * For distribution / production certificate use --> ssl://gateway.push.apple.com:2195
 */
define("IOS_APPLE_PUSH_SERVER", "");

/*
 * -----------------------------------------------------------------------------
 */
/*
 * Mysql specific details
 */
define("MYSQL_HOST", ""); // eg: localhost / xxx.xxx.xxx.xxx
define("MYSQL_USER", ""); // eg: admin_user
define("MYSQL_PASS", ""); // eg: 
define("MYSQL_DB", ""); // eg: taxi_db

/*
 * Mongodb specific details
 */
define("MONGODB_HOST", ""); // eg: localhost / xxx.xxx.xxx.xxx
define("MONGODB_USER", ""); // eg: admin_user
define("MONGODB_PASS", ""); // eg: BrBW5M99Xh!g^D@v
define("MONGODB_DB", ""); // eg: taxi_db
define("MONGODB_PORT", "27017"); // eg: 27017
/*
 * Mandrill api key
 *  Login/signup to the mandrill account at http://mandrillapp.com/
 *  Move on to settings, create an api key, paste it below here.
 *  yay, its that easy
 * Email id from which the user will see mails from.
 * Domain from which user will see the email from.  
 */
define("MANDRILL_API_KEY", "");//shripad account 

define("MANDRILL_FROM_NAME", ""); // eg: Support
define("MANDRILL_FROM_EMAIL", ""); // eg: info@domain.com
define("MANDRILL_FROM_EMAIL_BOOKINGS", ""); // eg: info@domain.com
define("MANDRILL_FROM_WEBSITE", "www.roadyo.in"); // eg: www.domain.com

/*
 *  Pubnub account keys, publish and subscribe keys
 */

// rahul@3embed.com account
define("PUBNUB_PUBLISH_KEY", ""); // eg: pub-c-xxxxxx-xxx-xxxxx-xxxxxxxxxx
define("PUBNUB_SUBSCRIBE_KEY", ""); // eg: sub-c-xxxxx-xxx-xxxxx-xxxxxxxxxx



/*
 *  Stripe api secret key
 *  If test secret key is used then the app must use the test publish key and same for live keys
 *  If you want to change the keys, you must remove all the stripe ids from slave table of mysql db (Old users will not have their cards, will have to add new cards)
 */

define("STRIPE_API_SECRET_KEY", "sk_live_XXXXXXXXXXX"); // eg: sk_test_xxxxxxxxxxxxxxxxxxx


