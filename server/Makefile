DOCKER_COMPOSE_FILE=docker-compose.yml
NGINX_SERVICE=nginx
IP_ADDRESS=192.168.1.200

RED=\033[0;31m
GREEN=\033[0;32m
LIGHT_GRAY=\033[0;37m
RESET=\033[0m
BOLD=\033[1m

https-on:
        @echo ""
        @echo -n "${BOLD}${LIGHT_GRAY}Stopping containers...${RESET} "
        @docker compose stop > /dev/null 2>&1
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Changing port binding for port 8080 in '$(DOCKER_COMPOSE_FILE)'...${RESET} "
        @sed -i 's/^ *- *80:80/      - 127.0.0.1:8080:80/' $(DOCKER_COMPOSE_FILE)
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Activating HTTPS mode with Nginx on port 80 and HTTPS on 443...${RESET} "
        @systemctl start $(NGINX_SERVICE)
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Restarting containers...${RESET} "
        @docker compose up -d > /dev/null 2>&1
        @echo "${GREEN}done${RESET}"

        @echo ""
        @echo "${BOLD}${GREEN}--------------- HTTPS mode activated ----------------${RESET}"
        @echo "${LIGHT_GRAY}You can now access the site via https://$(IP_ADDRESS)${RESET}\n"

https-off:
        @echo ""
        @echo -n "${BOLD}${LIGHT_GRAY}Stopping containers...${RESET} "
        @docker compose stop > /dev/null 2>&1
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Changing port binding for port 80 in '$(DOCKER_COMPOSE_FILE)'...${RESET} "
        @sed -i 's/^ *- *127.0.0.1:8080:80/      - 80:80/' $(DOCKER_COMPOSE_FILE)
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Disabling HTTPS mode with Nginx on port 80 and 443...${RESET} "
        @systemctl stop $(NGINX_SERVICE)
        @echo "${GREEN}done${RESET}"

        @echo -n "${BOLD}${LIGHT_GRAY}Restarting containers...${RESET} "
        @docker compose up -d > /dev/null 2>&1
        @echo "${GREEN}done${RESET}"

        @echo ""
        @echo "${BOLD}${RED}--------------- HTTPS mode disabled ----------------${RESET}"
        @echo "${LIGHT_GRAY}You can now access the site via http://$(IP_ADDRESS)${RESET}\n"