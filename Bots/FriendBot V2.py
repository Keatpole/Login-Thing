from getpass import getpass
import requests
import random
import string
import os
import sys

accountUsername = ""
accountPassword = ""
accountId = ""

friendDBId = ""

friends = 0

username = ""
password = ""
email = ""

url = "http://localhost/LoginThing/includes"

urls = {
    "signup": f"{url}/account/signup",
    "login": f"{url}/account/login",
    "logout": f"{url}/account/logout",
    "add": f"{url}/friends/add",
    "accept": f"{url}/friends/accept",
    "getuser": f"{url}/other/getUser"
}

s = requests.Session()
a = requests.Session()

times = 0

clear = "cls" if sys.platform == "win32" else "clear"

def start():
    global accountUsername, accountPassword, accountId, friendDBId, friends, times

    print("In order to accept the bot's friend requests on your behalf, we need your account details.")

    if accountUsername == "":
        accountUsername = input("\nWhat is your username?\n")

    if accountPassword == "":
        accountPassword = getpass("\nWhat is your password?\n")

        # Log in as the account provided
        user = a.post(urls["login"], data = {
            "uid": accountUsername,
            "pwd": accountPassword,
            "submit": ""
        })

        try:
            user.headers["Set-Cookie"]
            
            # Invalid username / password
            input("\nInvalid username or password!\n")
            
            sys.exit(0)

        except KeyError:
            print(f"\nLogged in as {accountUsername}\n")

    accountData = a.post(urls["getuser"], data = {
        "user": accountUsername
    }).text.split(",")

    try:
        if friendDBId == "":
            friendDBId = int(input("\nAdd someone as a friend, then hover over \"Remove Friend\" and look at the link in the bottom left. Type in what \"i\" is equal. (For example if the link ends in \"remove?u=2&i=1&t=req\" then type in \"1\")\n"))
    except Exception:
        friendDBId = ""
        
        print("\nPlease input a valid number.")
        start()

    try:
        friends = int(input("\nHow many friends do you want to get?\n"))
    except ValueError:
        print("\nPlease input a valid number.")
        start()
    
    os.system(clear)

    while times < friends:
        # Generate user info
        global username, password, email

        username = ""
        password = ""
        email = ""

        friendDBId += 1

        for _ in range(random.randint(4, 10)):
            username += random.choice(string.ascii_letters)
        for _ in range(random.randint(7, 20)):
            password += random.choice(string.ascii_letters)

        email = f"{username}@{random.choice(['gmail', 'yahoo', 'outlook', 'hotmail'])}.com"

        # Create a user with that info
        s.post(urls["signup"], data = {
            "uid": username,
            "email": email,
            "pwd": password,
            "pwdrepeat": password,
            "submit": ""
        })

        # Log in as that user
        s.post(urls["login"], data = {
            "uid": username,
            "pwd": password,
            "submit": ""
        })

        accountData2 = s.post(urls["getuser"], data = {
            "user": username
        }).text.split(",")


        # Sends the account provided a friend request 
        s.post(urls["add"], data = {
            "user": accountData[0].split(":")[1],
            "submit": ""
        })

        # Accepts the friend requests as the account provided
        a.post(urls["accept"], data = {
            "user": accountData2[0].split(":")[1],
            "id": friendDBId,
            "submit": ""
        })

        # Logs out of that user
        s.post(urls["logout"])

        times += 1

        print(f"+{times} friends.")

    input("\nDone!\n")

    os.system(clear)
    sys.exit(0)


if __name__ == '__main__':
    os.system(clear)
    start()