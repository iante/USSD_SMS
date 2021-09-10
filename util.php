<?php

class Util{

    //DB VARIABLES
    static $DB_NAME = "ussdsms";
    static $DB_USER = "root";
    static $DB_USER_PASS = "";
    static $SERVER_NAME = "localhost";

    //About USSD Menu
    //Creating Static Variable for go back & go to main menu options
    /*Making them static so that they can be accessed without
    having to create an object of the class Util*/

    static $GO_BACK = "98";
    static $GO_TO_MAIN_MENU = "99";

    static $USER_BALANCE = 4000;

    //Country Code
    static $COUNTRY_CODE = "+254";

    //transaction cost
    static $TRANSACTION_COST = "50";


    //AT CONSTANTS
    static $API_KEY = "09a16dd0e834f3b385889fd86319a29379e1c28420e50e1775a287917ca45092";
    static $API_USERNAME = "sandbox";
    static $SMS_SHORTCODE = 73256;
    static $COMPANY_NAME = "H&S GROUP";
}
?>