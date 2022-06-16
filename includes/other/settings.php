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
    public $hide_replies = 1; # If replies are hidden. If enabled, replies can only be seen be clicking "Reply" on a comment, as for now, having this option enabled disable deleting replies as a side effect.

    public $enable_random_comments = 0; # If random comments are enabled, there will be a random chance of seeing a comment.
    public $random_comment_chance = 3; # 1 in x chance the comment being skipped
    
    public $enable_suggestions = 1; # If admins can accept suggestions
    public $enable_mod_panel = 1; # If the mod panel should be enabled
    public $enable_admin_panel = 1; # If the admin panel should be enabled

    public $enable_eval_private = 1; # if eval should work for everyone with owner rank but only on localhost, if you don't know what this is, set it to 0
    public $enable_eval_public = 0; # if eval should work for everyone with owner rank, if you don't know what this is, keep it at 0

    public $enable_public = 0; # if the site should be public or not, if this is set to 0, the site will only work on "localhost"
}

$default = new Settings();

$dev = new Settings();

$dev->enable_login = 1; # Enable logging in
$dev->enable_signup = 1; # Enable signing up
$dev->enable_reset_pass = 1; # Enable resetting passwords
$dev->enable_friends = 1; # Enable adding friends
$dev->enable_report = 1; # Enable reporting of users
$dev->enable_appeal = 1; # Enable appealing

$dev->enable_posting_comments = 1; # If users can post comments
$dev->enable_viewing_comments = 1; # If users can view comments
$dev->enable_likes = 1; # If users can like comments
$dev->hide_replies = 1; # If replies are hidden. If enabled, replies can only be seen be clicking "Reply" on a comment, as for now, having this option enabled disable deleting replies as a side effect.

$dev->enable_random_comments = 0; # If random comments are enabled, there will be a random chance of seeing a comment.
$dev->random_comment_chance = 3; # 1 in x chance the comment being skipped

$dev->enable_suggestions = 1; # If admins can accept suggestions
$dev->enable_mod_panel = 1; # If the mod panel should be enabled
$dev->enable_admin_panel = 1; # If the admin panel should be enabled

$dev->enable_eval_private = 1; # if eval should work for everyone with owner rank but only on localhost, if you don't know what this is, set it to 0
$dev->enable_eval_public = 0; # if eval should work for everyone with owner rank, if you don't know what this is, keep it at 0

$dev->enable_public = 0; # if the site should be public or not, if this is set to 0, the site will only work on "localhost"


$prod = new Settings();

$prod->enable_login = 1; # Enable logging in
$prod->enable_signup = 1; # Enable signing up
$prod->enable_reset_pass = 1; # Enable resetting passwords
$prod->enable_friends = 1; # Enable adding friends
$prod->enable_report = 1; # Enable reporting of users
$prod->enable_appeal = 1; # Enable appealing

$prod->enable_posting_comments = 1; # If users can post comments
$prod->enable_viewing_comments = 1; # If users can view comments
$prod->enable_likes = 1; # If users can like comments
$prod->hide_replies = 1; # If replies are hidden. If enabled, replies can only be seen be clicking "Reply" on a comment, as for now, having this option enabled disable deleting replies as a side effect.

$prod->enable_random_comments = 0; # If random comments are enabled, there will be a random chance of seeing a comment.
$prod->random_comment_chance = 3; # 1 in x chance the comment being skipped

$prod->enable_suggestions = 1; # If admins can accept suggestions
$prod->enable_mod_panel = 1; # If the mod panel should be enabled
$prod->enable_admin_panel = 1; # If the admin panel should be enabled

$prod->enable_eval_private = 0; # if eval should work for everyone with owner rank but only on localhost, if you don't know what this is, set it to 0
$prod->enable_eval_public = 0; # if eval should work for everyone with owner rank, if you don't know what this is, keep it at 0

$prod->enable_public = 1; # if the site should be public or not, if this is set to 0, the site will only work on "localhost"

# Replace $default with $dev or $prod depending on the environment you are in.
$settings = $default;