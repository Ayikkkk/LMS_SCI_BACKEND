#!/bin/bash
# Script optimasi PHP-FPM untuk penggunaan massal
# Jalankan: bash optimize_fpm.sh
# Harus dijalankan sebagai root

echo "=== Cek spesifikasi server ==="
TOTAL_RAM_MB=$(free -m | awk 'NR==2{print $2}')
CPU_CORES=$(nproc)
echo "RAM: ${TOTAL_RAM_MB}MB"
echo "CPU cores: ${CPU_CORES}"

# Hitung workers berdasarkan RAM
# Setiap PHP-FPM worker butuh ~30-50MB RAM
# Sisakan 30% RAM untuk OS, MySQL, nginx
USABLE_RAM=$((TOTAL_RAM_MB * 70 / 100))
MAX_CHILDREN=$((USABLE_RAM / 40))
START_SERVERS=$((MAX_CHILDREN / 4))
MIN_SPARE=$((MAX_CHILDREN / 4))
MAX_SPARE=$((MAX_CHILDREN / 2))

# Minimal 5, maksimal 50 workers
if [ $MAX_CHILDREN -lt 5 ]; then MAX_CHILDREN=5; fi
if [ $MAX_CHILDREN -gt 50 ]; then MAX_CHILDREN=50; fi
if [ $START_SERVERS -lt 2 ]; then START_SERVERS=2; fi
if [ $MIN_SPARE -lt 2 ]; then MIN_SPARE=2; fi

echo ""
echo "=== Konfigurasi yang akan diterapkan ==="
echo "pm.max_children = $MAX_CHILDREN"
echo "pm.start_servers = $START_SERVERS"
echo "pm.min_spare_servers = $MIN_SPARE"
echo "pm.max_spare_servers = $MAX_SPARE"
echo "pm.max_requests = 500  (recycle worker setiap 500 request)"

FPM_CONF="/etc/php/8.5/fpm/pool.d/www.conf"

if [ ! -f "$FPM_CONF" ]; then
    echo "❌ File $FPM_CONF tidak ditemukan"
    exit 1
fi

# Backup config lama
cp "$FPM_CONF" "${FPM_CONF}.bak.$(date +%Y%m%d)"
echo ""
echo "=== Backup config lama ==="
echo "Tersimpan di ${FPM_CONF}.bak.$(date +%Y%m%d)"

# Apply konfigurasi
sed -i "s/^pm = .*/pm = dynamic/" "$FPM_CONF"
sed -i "s/^pm\.max_children = .*/pm.max_children = $MAX_CHILDREN/" "$FPM_CONF"
sed -i "s/^pm\.start_servers = .*/pm.start_servers = $START_SERVERS/" "$FPM_CONF"
sed -i "s/^pm\.min_spare_servers = .*/pm.min_spare_servers = $MIN_SPARE/" "$FPM_CONF"
sed -i "s/^pm\.max_spare_servers = .*/pm.max_spare_servers = $MAX_SPARE/" "$FPM_CONF"

# Set max_requests jika belum ada
if grep -q "^pm.max_requests" "$FPM_CONF"; then
    sed -i "s/^pm\.max_requests = .*/pm.max_requests = 500/" "$FPM_CONF"
else
    echo "pm.max_requests = 500" >> "$FPM_CONF"
fi

# Restart FPM
echo ""
echo "=== Restart PHP-FPM ==="
systemctl restart php8.5-fpm
if [ $? -eq 0 ]; then
    echo "✅ PHP-FPM berhasil direstart"
else
    echo "❌ Gagal restart PHP-FPM"
    exit 1
fi

echo ""
echo "=== Verifikasi konfigurasi aktif ==="
grep -E "^pm|^pm\." "$FPM_CONF"

echo ""
echo "=== Selesai ==="
echo "Server siap untuk $MAX_CHILDREN concurrent users"
