---
name: location-status-quick
description: |
  Reporte rápido del estado del Glosario de Ubicaciones desde sqlite.
  Devuelve conteos de pendientes, completadas y completadas sin reescribir.
  Usar cuando el usuario pregunte cuántas ubicaciones faltan, cuántas están sin
  contenido o cuál es el estado general del glosario.
---

# Skill: location-status-quick

Reporte rápido del estado del Glosario de Ubicaciones.

## Pipeline

### 1. Ejecutar consulta sqlite

```bash
cd C:\own\wp_bc\tracking
python -c "
import sqlite3
conn = sqlite3.connect('locations.db')
cur = conn.cursor()
cur.execute(\"SELECT COUNT(*) FROM locations WHERE status='pending'\")
print('Pendientes:', cur.fetchone()[0])
cur.execute(\"SELECT COUNT(*) FROM locations WHERE status='completed' AND rewritten=1\")
print('Completadas reescritas:', cur.fetchone()[0])
cur.execute(\"SELECT COUNT(*) FROM locations WHERE status='completed' AND rewritten=0\")
print('Completadas NO reescritas:', cur.fetchone()[0])
conn.close()
"
```

### 2. Entregar solo el resultado

No explicar, no analizar, no hacer follow-up. Solo mostrar los tres números.

Si el usuario pide desglose por relevancia, agregar:

```bash
python -c "
import sqlite3
conn = sqlite3.connect('locations.db')
cur = conn.cursor()
for r in [(3,'Alta'),(2,'Media'),(1,'Baja')]:
    cur.execute('SELECT COUNT(*) FROM locations WHERE status=? AND relevancia=?', ('pending', r[0]))
    print(f'Pendientes {r[1]}:', cur.fetchone()[0])
    cur.execute('SELECT COUNT(*) FROM locations WHERE status=? AND relevancia=?', ('completed', r[0]))
    print(f'Completadas {r[1]}:', cur.fetchone()[0])
conn.close()
"
```
