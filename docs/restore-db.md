# Restauración de la base de datos

El contenedor `wp_bc_cli` ejecuta un dump automático cada 10 minutos y mantiene los dos archivos más recientes en `backups/`.

## Archivos

| Archivo | Contenido |
|---------|-----------|
| `backups/db-latest.sql` | Dump más reciente (actualizado cada 10 min) |
| `backups/db-previous.sql` | Dump anterior (sobreescribe al generar uno nuevo) |

## Restaurar en la misma máquina

```bash
docker exec wp_bc_cli wp db import /var/www/html/backups/db-latest.sql --allow-root
```

## Reconstruir desde cero en otra máquina

```bash
# 1. Clonar el repo
git clone https://github.com/JPMarichal/wp_bc.git
cd wp_bc

# 2. Levantar contenedores
docker compose up -d

# 3. Esperar a que WordPress y BD estén listos
docker compose ps

# 4. Importar el backup más reciente
docker exec wp_bc_cli wp db import /var/www/html/backups/db-latest.sql --allow-root

# 5. (Opcional) Reemplazar URLs si el dominio cambió
docker exec wp_bc_cli wp search-replace "http://localhost:8080" "https://nuevo-dominio.com" --allow-root
```

## Notas

- No se requiere Windows Task Scheduler ni cron. El contenedor `cli` tiene un loop interno que ejecuta `scripts/dump-db.sh` cada 600 segundos.
- `db-data/` está en `.gitignore` y no se respalda en git. Solo los dumps SQL en `backups/` viajan con el repo.
- Los dumps son texto plano (SQL) — se pueden diffear y mergear.
- Si se necesita restaurar un punto intermedio, usar `db-previous.sql`.
