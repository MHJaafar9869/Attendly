#!/bin/bash

# Colors
GREEN="\e[32m"
RED="\e[31m"
CYAN="\e[36m"
RESET="\e[0m"

# Prompt (default = docker)
read -p "Start application as (local/docker)? [docker]: " environment

# Default if empty
environment=${environment:-docker}

# Get only the first letter (lowercase)
first_char=$(echo "${environment:0:1}" | tr '[:upper:]' '[:lower:]')

case "$first_char" in
    d)
        clear
        echo -e "${CYAN}Starting application in ${GREEN}DOCKER${CYAN} mode...${RESET}"
        ./Docker/docker-dev.sh
        ;;
    l)
        clear
        echo -e "${CYAN}Starting application in ${GREEN}LOCAL${CYAN} mode...${RESET}"
        ./Docker/local-dev.sh
        ;;
    *)
        echo -e "${RED}Invalid option.${RESET} Start with 'd' for docker or 'l' for local."
        exit 1
        ;;
esac
