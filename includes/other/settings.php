<?php

class Settings {
    public $enable_login = 1;
    public $enable_signup = 1;
    public $enable_reset_pass = 1;
    public $enable_friends = 1;
    public $enable_report = 1;

    public $enable_posting_comments = 1;
    public $enable_viewing_comments = 1;
    public $enable_likes = 1;
    
    public $enable_suggestions = 1;
    public $enable_mod_panel = 1;
    public $enable_admin_panel = 1;

    public $enable_public = 0;
}

$settings = new Settings();