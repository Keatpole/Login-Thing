import requests, random, string, os, sys

commentId = ""
likes = 0

username = ""
password = ""
email = ""

url = "http://localhost/LoginThing/includes"

urls = {
    "signup": f"{url}/account/signup",
    "login": f"{url}/account/login",
    "delete": f"{url}/account/delete",
    "like": f"{url}/comments/like"
}

s = requests.Session()
times = 0

clear = "cls" if sys.platform == "win32" else "clear"

def start():
    global commentId, likes, times

    if commentId == "":
        commentId = input("\nWhat is the comment id? To find it, hover over \"Reply\" on your comment, and look in the bottom left corner to see the link.\nThe last number of the link is the comment id.\n")
        
    try:
        likes = int(input("\nHow many likes do you want your comment to get?\n"))
    except ValueError:
        print("\nPlease input a valid number.")
        start()
    
    os.system(clear)

    while times < likes:
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

        # Like the comment as that user
        s.post(urls["like"], data = {
            "commentId": commentId,
            "submit": ""
        })

        # Delete the account of that user
        s.post(urls["delete"])

        times += 1
        print(f"+{times} likes.")

    input("\nDone!\n")

    os.system(clear)
    sys.exit(0)


if __name__ == '__main__':
    os.system(clear)
    start()