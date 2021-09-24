import requests, random, string, threading

url = "http://localhost/LoginThing/includes"

def start():    
    while True:

        # Generate user info
        global username, email

        username = ""
        email = ""

        for _ in range(random.randint(4, 10)):
            username += random.choice(string.ascii_letters)

        email = f"{username}@{random.choice(['gmail', 'yahoo', 'outlook', 'hotmail', 'icloud'])}.com"

        # Create a user with that info
        requests.post(f"{url}/account/signup", data = {
            "uid": username,
            "email": email,
            "pwd": "a",
            "pwdrepeat": "a",
            "submit": ""
        })

        print(f"{email}")


if __name__ == '__main__':

    threads = []

    for i in range(50):
        t = threading.Thread(target=start)
        t.daemon = True
        threads.append(t)

    for i in range(50):
        threads[i].start()

    for i in range(50):
        threads[i].join()

    
