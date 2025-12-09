#!/bin/bash

# =========================
# Colors
# =========================
GREEN="\e[32m"
RED="\e[31m"
CYAN="\e[36m"
YELLOW="\e[33m"
RESET="\e[0m"

echo -e "${CYAN}[Local] Switching to LOCAL environment...${RESET}"

# =========================
# Check & Create .env.local
# =========================
if [ ! -f .env.local ]; then
    echo -e "${YELLOW}[Local] .env.local not found. Creating from .env.example...${RESET}"
    cp .env.example .env.local
    echo -e "${GREEN}[Local] ✓ .env.local was created${RESET}"
fi

# =========================
# Link .env → .env.local
# =========================
ln -sf .env.local .env
echo -e "${GREEN}[Local] ✓ .env now points to .env.local${RESET}"

# =========================
# Ask if migrations should run
# =========================
read -p "Run migrations? (yes/No): " runMig
first_char=$(echo "${runMig:0:1}" | tr '[:upper:]' '[:lower:]')

echo -e "${CYAN}[Local] Clearing caches...${RESET}"
php artisan optimize:clear

echo -e "${CYAN}[Local] Running storage link...${RESET}"
php artisan storage:link

if [[ "$first_char" == "y" ]]; then
    echo -e "${CYAN}[Local] Running migrations...${RESET}"
    php artisan migrate --force
    echo -e "${GREEN}[Local] ✓ Migrations completed${RESET}"
else
    echo -e "${YELLOW}[Local] Skipped migrations${RESET}"
fi

# =========================
# Start the Laravel Dev Server
# =========================
echo -e "${CYAN}[Local] Starting Laravel development server...${RESET}"
php artisan serve
