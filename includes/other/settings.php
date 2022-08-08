<?php

class Settings {
    public $enable_login = 1; # Enable logging in
    public $enable_signup = 1; # Enable signing up
    public $enable_reset_pass = 1; # Enable resetting passwords
    public $enable_friends = 1; # Enable adding friends
    public $enable_report = 1; # Enable reporting of users
    public $enable_appeal = 1; # Enable appealing

    public $enable_posting_comments = 1; # If users can post comments
    public $enable_viewing_comments = 1; # If users can view comments
    public $enable_likes = 1; # If users can like comments
    
    public $enable_suggestions = 1; # If admins can accept suggestions
    public $enable_mod_panel = 1; # If the mod panel should be enabled
    public $enable_admin_panel = 1; # If the admin panel should be enabled

    public $enable_eval_private = 1; # if eval should work for everyone with owner rank but only on localhost, if you don't know what this is, set it to 0
    public $enable_eval_public = 0; # if eval should work for everyone with owner rank, if you don't know what this is, keep it at 0

    public $enable_public = 0; # if the site should be public or not, if this is set to 0, the site will only work on "localhost"
}

# --- IF YOU DON'T KNOW WHAT YOU ARE DOING, ONLY EDIT THE LINES ABOVE THIS LINE ---

$default = new Settings();

$dev = new Settings();

$dev->enable_login = 1;
$dev->enable_signup = 1;
$dev->enable_reset_pass = 1;
$dev->enable_friends = 1;
$dev->enable_report = 1;
$dev->enable_appeal = 1;

$dev->enable_posting_comments = 1;
$dev->enable_viewing_comments = 1;
$dev->enable_likes = 1;

$dev->enable_suggestions = 1;
$dev->enable_mod_panel = 1;
$dev->enable_admin_panel = 1;

$dev->enable_eval_private = 1;
$dev->enable_eval_public = 0;

$dev->enable_public = 0;


$prod = new Settings();

$prod->enable_login = 1;
$prod->enable_signup = 1;
$prod->enable_reset_pass = 1;
$prod->enable_friends = 1;
$prod->enable_report = 1;
$prod->enable_appeal = 1;

$prod->enable_posting_comments = 1;
$prod->enable_viewing_comments = 1;
$prod->enable_likes = 1;

$prod->enable_suggestions = 1;
$prod->enable_mod_panel = 1;
$prod->enable_admin_panel = 1;

$prod->enable_eval_private = 0;
$prod->enable_eval_public = 0;

$prod->enable_public = 1;

# Replace $default with $dev or $prod depending on the environment you are in.
$settings = $default;