 # Instalación

## Requisitos

- Windows 10/11 con [Laragon](https://laragon.org/) (Apache + MySQL).
- PHP 8.2 (incluido en Laragon, en `C:\laragon\bin\php\php-8.2`).
- MySQL 8.0 (incluido en Laragon).
- Python 3.10+ (Laragon trae 3.13 en `C:\laragon\bin\python\python-3.13`, sirve).

## Pasos

### 1. Ubicar el proyecto

Copiar o clonar el directorio `hotel-luna-azul/` en `C:\laragon\www\`.

Laragon autodetecta y crea el virtualhost `hotel-luna-azul.test`. Si no, abrir `http://localhost/hotel-luna-azul/public/`.

### 2. Crear la base de datos

Desde la terminal de Laragon (Cmder) o PowerShell:

```
"C:\laragon\bin\mysql\mysql-8.0.40-winx64\bin\mysql.exe" -u root -e "CREATE DATABASE hotel_luna_azul CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

(Ajustar la ruta de mysql si su versión es distinta.)

### 3. Cargar el esquema y los datos

```
mysql --default-character-set=utf8mb4 -u root hotel_luna_azul < database\schema.sql
mysql --default-character-set=utf8mb4 -u root hotel_luna_azul < database\seed.sql
```

> **Importante:** ejecutar el comando con redirección directa (`<`), no con pipeline de PowerShell (`Get-Content | mysql`). PowerShell convierte tildes a `?` al pasar por pipeline. Si por alguna razón se ven nombres como "Mej??a", recargar el seed con el comando de arriba desde `cmd.exe` o con `mysql ... < archivo.sql`.

### 4. Crear el entorno virtual de Python

```
C:\laragon\bin\python\python-3.13\python.exe -m venv python\venv
python\venv\Scripts\pip install -r python\requirements.txt
``` 

Esto instala `reportlab` y `pillow`, las dependencias del componente de PDFs.

### 5. Configurar el `.env`

El archivo `.env` ya viene listo, pero si la ruta de Python o credenciales de MySQL difieren, ajustar:

```
DB_HOST=127.0.0.1
DB_USER=root
DB_PASS=
DB_NAME=hotel_luna_azul

PYTHON_BIN="C:/laragon/www/hotel-luna-azul/python/venv/Scripts/python.exe"
```

### 6. Composer (opcional)

El autoload PSR-4 funciona sin Composer (autoloader manual incluido). Si quiere optimizado:

```
"C:\laragon\bin\composer\composer.bat" dump-autoload -o
```

### 7. Verificar

Abrir `http://hotel-luna-azul.test/` y entrar con:

- `admin@lunaazul.test` / `LunaAzul2026!`

Si llega al panel y ve "Reservas activas: 4", todo funciona.

## Probar la generación de PDF

Desde la ficha de cualquier reserva → "Voucher PDF". Si no se genera:

1. Verificar que `python\venv\Scripts\python.exe` exista.
2. Ejecutar a mano:
   ```
   python\venv\Scripts\python.exe python\generate_reservation_pdf.py --data "{\"code\":\"TEST\",\"first_name\":\"Test\",\"last_name\":\"User\"}" --out test.pdf
   ```
3. Si reportlab falta: `python\venv\Scripts\pip install reportlab`.

## Solución de problemas

- **"Acceso denegado" a MySQL**: verificar que el usuario root no tenga clave o ajustar `DB_PASS` en `.env`.
- **404 en todas las rutas**: confirmar que Apache tiene `mod_rewrite` activo (Laragon lo trae así por defecto).
- **PDF no se genera**: revisar `storage/reports/` y permisos. Confirmar que el venv tiene reportlab.
- **Errores de sesión / CSRF**: borrar cookie del navegador y volver a entrar.
