import requests
import random
import string
import os
import sys

accountUsername = ""

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
    "getuser": f"{url}/other/getUser"
}

s = requests.Session()

times = 0

clear = "cls" if sys.platform == "win32" else "clear"

def start():
    global accountUsername, friends, times

    if accountUsername == "":
        accountUsername = input("What is your username?\n")

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

        # Sends the account provided a friend request 
        s.post(urls["add"], data = {
            "user": s.post(urls["getuser"], data = {"user": accountUsername}).text.split(",")[0].split(":")[1],
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