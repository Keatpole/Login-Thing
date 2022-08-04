# Login-Thing

## How to install
First, press the button labelled `Code` then click `Download ZIP`

Then [Download XAMPP](https://www.apachefriends.org), once the download is finished, open it and follow the instructions.

Once the download is finished, open the XAMPP folder and navigate to the folder called `htdocs` and delete whatever is inside. Open the zip file and extract the folder inside it to your `htdocs` folder.

Now start XAMPP in administrator mode. Once it is opened click the `X` next to `Apache` and `MySQL`. If you can't see the `X`, try to open XAMPP in administrator mode.

Once the `X` next to them is a checkmark, click `Start` next to them. Once you have done that [Click Here](http://localhost:80/phpmyadmin).

Once the website pops up, click `New` to the left side of the website. Where it says `Database name`, put in `website` exactly and press `Create`. Once it's done, click `website` to the left side of the screen, under `New`. Now click `Import` at the top middle of the screen.
Once you are there click the `Browse...` button and navigate to your XAMPP folder. Once you are there open up your `htdocs` and then `Login-Thing-main`. Inside there should be a file called `.ht-website.sql`, select it.

Once it is done, [Click Here](http://localhost:80/Login-Thing-main) and the website should open.

Click `Login` and for the username write `Test` and the password is `123`. This will log you into an account with owner access.
If you want to change some settings, open the XAMPP folder, open `htdocs` `Login-Thing-main` `includes` `other` and open `settings.php` in notepad.

### Password Reset Process
Someone clicks forgot password and types in their username or email, then a moderator or above has to go to groups and find the group
labeled `Mod Help (their username)` and somehow verify that it is them. If they are, they need to contact the owner.
The owner has to go to `Home` and type in `!recover <username>` in the chat then press `Confirm`. This will take the owner to an all white page with a link and potentially a warning. Ignore the warning. The owner will then have to copy the link and go back to the group and paste it in the chat. The user will then have to copy the link and paste it into their browser. They can now reset their password or change their username.
The group will be automatically deleted upon password / username change.
