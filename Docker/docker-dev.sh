#!/bin/bash

# =========================
# Colors
# =========================
GREEN="\e[32m"
RED="\e[31m"
CYAN="\e[36m"
YELLOW="\e[33m"
RESET="\e[0m"

echo -e "${CYAN}[Docker] Switching to Docker environment...${RESET}"

# =========================
# Check & Create .env.docker
# =========================
if [ ! -f .env.docker ]; then
    echo -e "${YELLOW}[Docker] .env.docker not found. Creating from .env.example...${RESET}"
    cp .env.example .env.docker

    echo -e "${CYAN}[Docker] Updating APP_PORT, APP_URL and DB_HOST...${RESET}"

    # Reset keys cleanly
    sed -i "/^DB_HOST=/d" .env.docker
    sed -i "/^APP_PORT=/d" .env.docker
    sed -i "/^APP_URL=/d" .env.docker

    if command -v hostname &> /dev/null; then
        LOCAL_IP=$(hostname -I | awk '{print $1}')
    elif command -v ip &> /dev/null; then
        LOCAL_IP=$(ip route get 1 | awk '{print $7; exit}')
    else
        LOCAL_IP=127.0.0.1
    fi

    # Write new values
    {
        echo "DB_HOST=mysql"
        echo "APP_PORT=81"
        echo "APP_URL=http://$LOCAL_IP:81"
    } >> .env.docker

    echo -e "${GREEN}[Docker] ✓ .env.docker created and updated.${RESET}"
fi

# =========================
# Link main .env → .env.docker
# =========================
ln -sf .env.docker .env
echo -e "${GREEN}[Docker] ✓ .env now points to .env.docker${RESET}"

# =========================
# Detect host UID/GID
# =========================
export HOST_UID=$(id -u)
export HOST_GID=$(id -g)

echo -e "${CYAN}[Docker] Host UID: $HOST_UID${RESET}"
echo -e "${CYAN}[Docker] Host GID: $HOST_GID${RESET}"

# Clean existing HOST_UID/GID
sed -i "/^HOST_UID=/d" .env.docker
sed -i "/^HOST_GID=/d" .env.docker

{
    echo "HOST_UID=$HOST_UID"
    echo "HOST_GID=$HOST_GID"
} >> .env.docker

# =========================
# Ask user for fresh build
# =========================
read -p "Would you like a fresh build? (yes/No): " answer
first_char=$(echo "${answer:0:1}" | tr '[:upper:]' '[:lower:]')

echo -e "${YELLOW}[Docker] Stopping containers...${RESET}"
docker compose down

if [[ "$first_char" == "y" ]]; then
    echo -e "${CYAN}[Docker] Removing volumes...${RESET}"

    docker volume rm attendly_mysql-data 2>/dev/null || true
    docker volume rm attendly_redis-data 2>/dev/null || true

    echo -e "${CYAN}[Docker] Rebuilding containers...${RESET}"
    docker compose build --build-arg HOST_UID=$HOST_UID --build-arg HOST_GID=$HOST_GID
else
    echo -e "${CYAN}[Docker] Skipping fresh build.${RESET}"
fi

# =========================
# Start containers
# =========================
echo -e "${CYAN}[Docker] Starting Docker containers...${RESET}"
docker compose up -d && clear

sleep 1

APP_URL="$(docker compose exec app bash -c "echo \$APP_URL")"
echo -e "${GREEN}[Docker] ✓ Containers started at: ${CYAN}$APP_URL${RESET}"

# =========================
# Ask user if they'd like to show logs
# =========================
read -p "Would you like to see logs? (yes/No): " answer
first_char=$(echo "${answer:0:1}" | tr '[:upper:]' '[:lower:]')

if [[ "$first_char" == "y" ]]; then
    docker compose logs -f
fi
