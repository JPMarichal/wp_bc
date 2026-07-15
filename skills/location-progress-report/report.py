#!/usr/bin/env python3
"""
location-progress-report skill
Efficient progress reporting for the Bible location regeneration pipeline.
Shows total, completed, pending, and breakdown by relevancia level.
"""

import sys
import os
from datetime import datetime

# Add tracking directory to path
sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', '..', 'tracking'))

from tracker import get_conn

def main():
    conn = get_conn()
    cursor = conn.cursor()

    cursor.execute('SELECT COUNT(*) FROM locations')
    total = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE status = "completed"')
    completed = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE status != "completed"')
    pending = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 3')
    alta = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 3 AND status = "completed"')
    alta_done = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 2')
    media = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 2 AND status = "completed"')
    media_done = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 1')
    baja = cursor.fetchone()[0]

    cursor.execute('SELECT COUNT(*) FROM locations WHERE relevancia = 1 AND status = "completed"')
    baja_done = cursor.fetchone()[0]

    conn.close()

    alta_pct = (alta_done / alta * 100) if alta > 0 else 0
    media_pct = (media_done / media * 100) if media > 0 else 0
    baja_pct = (baja_done / baja * 100) if baja > 0 else 0
    total_pct = (completed / total * 100) if total > 0 else 0

    print(f'Reporte de avance - {datetime.now().strftime("%Y-%m-%d %H:%M:%S")}')
    print('')
    print(f'Total: {total}')
    print(f'Completadas: {completed}')
    print(f'Pendientes: {pending}')
    print('')
    print(f'Alta (3): {alta} (hechas: {alta_done}, faltan: {alta - alta_done}, {alta_pct:.1f}%)')
    print(f'Media (2): {media} (hechas: {media_done}, faltan: {media - media_done}, {media_pct:.1f}%)')
    print(f'Baja (1): {baja} (hechas: {baja_done}, faltan: {baja - baja_done}, {baja_pct:.1f}%)')
    print('')
    print(f'Progreso total: {total_pct:.1f}%')

    # Show next batch if there are pending Alta locations
    if alta - alta_done > 0:
        sys.path.insert(0, os.path.join(os.path.dirname(__file__), '..', '..', 'tracking'))
        from tracker import get_regeneration_queue
        queue = get_regeneration_queue(10, 3)
        if queue:
            next_wp_ids = [str(row['wp_id']) for row in queue]
            print(f'\nSiguiente lote Alta (wp_ids): {", ".join(next_wp_ids)}')

if __name__ == '__main__':
    main()
