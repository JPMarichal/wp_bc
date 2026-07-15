from tracker import get_conn
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
print('Total:', total)
print('Completadas:', completed)
print('Pendientes:', pending)
print('Alta:', alta, '(hechas:', alta_done, ', faltan:', alta - alta_done, ')')
print('Media:', media, '(hechas:', media_done, ', faltan:', media - media_done, ')')
print('Baja:', baja, '(hechas:', baja_done, ', faltan:', baja - baja_done, ')')
