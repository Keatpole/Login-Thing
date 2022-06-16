# Login-Thing

## How to install
### Main
First, press the button labelled `Code` then click `Download ZIP`

Then [Download XAMPP](https://www.apachefriends.org/xampp-files/8.1.1/xampp-windows-x64-8.1.1-2-VS16-installer.exe), once the download is finished, open it and follow the instructions.

Once the download is finished, open the XAMPP folder and navigate to the folder called `htdocs` and delete whatever is inside. Open the zip file and extract the folder inside it to your `htdocs` folder.

Now start XAMPP in administrator mode. Once it is opened click the `X` next to `Apache` and `MySQL`. If you can't see the `X`, try to open XAMPP in administrator mode.

Once the `X` next to them is a checkmark, click `Start` next to them. Once you have done that [Click Here](http://localhost:80/phpmyadmin).

Once the website pops up, click `New` to the left side of the website. Where it says `Database name`, put in `website` exactly and press `Create`. Once it's done, click `website` to the left side of the screen, under `New`. Now click `Import` at the top of the screen. Once you are there click the `Choose` button and navigate to your XAMPP folder. Once you are there open up your `htdocs` and then `Login-Thing-main`. Inside there should be a file called `.ht-website.sql`, select it.

Once it is done, [Click Here](http://localhost:80/Login-Thing-main) and the website should open.

Click `Login` and for the username write `Test` and the password is `123`. This will log you into an account will owner access.
If you want to change some settings, open the XAMPP folder, open `htdocs` `Login-Thing-main` `includes` `other` and open `settings.php` in notepad.

### Enable Reset Password
If you want password resetting to work, ~~create a gmail account. Once you are logged in, [Click Here](https://myaccount.google.com/security) and make sure that `2-Step Verification` is off, then [Click Here](https://myaccount.google.com/u/0/lesssecureapps) and enable it.~~ (After May 30th, 2022; This no longer works. Try to find an alternative.)

Then navigate to your XAMPP folder and open the folder called `sendmail` inside there should be a file called `sendmail.ini` open it change `smtp_server` to be equal to ~~`smtp.gmail.com`~~ then set `smtp_port` to ~~`587`~~, scroll all the way down and find `force_sender` and set it to the email you created, for example ~~`example@gmail.com`~~ then find `auth_username` and set it to the email you created and set `auth_password` to the password.
