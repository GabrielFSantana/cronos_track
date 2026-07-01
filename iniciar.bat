@echo off
setlocal enabledelayedexpansion
title Cronos Track

rem Este script assume que a configuracao inicial de cada componente ja foi
rem feita (composer install, api/.env preenchido, venv do agente criado com
rem as dependencias instaladas). Veja o readme.md para o primeiro setup.

set "ROOT=%~dp0"

for /d %%D in ("C:\laragon\bin\php\php-*") do set "PHP_DIR=%%D"
for /d %%D in ("C:\laragon\bin\mysql\mysql-*") do set "MYSQL_DIR=%%D\bin"
set "PATH=%PHP_DIR%;%MYSQL_DIR%;%PATH%"

echo ============================================
echo   Cronos Track - inicializando ambiente
echo ============================================
echo.

echo [1/3] Verificando MySQL...
tasklist /FI "IMAGENAME eq mysqld.exe" 2>nul | find /I "mysqld.exe" >nul
if errorlevel 1 (
    echo   MySQL nao esta rodando. Abra o Laragon e clique em "Start All" antes de continuar.
    pause
    exit /b 1
)
echo   MySQL OK.
echo.

echo [2/3] Iniciando API + Dashboard (php spark serve)...
start "Cronos - API" cmd /k "cd /d "%ROOT%api" && php spark serve --host 127.0.0.1"
timeout /t 3 /nobreak >nul

echo [3/3] Iniciando agente Windows (captura de app ativo)...
start "Cronos - Agent" cmd /k "cd /d "%ROOT%agent" && .venv\Scripts\python.exe main.py"
timeout /t 2 /nobreak >nul

echo.
echo Abrindo o dashboard no navegador...
start "" "http://localhost:8080"

echo.
echo ============================================
echo   Tudo iniciado!
echo   - Janela "Cronos - API": servidor da API/dashboard
echo   - Janela "Cronos - Agent": agente de rastreamento
echo   Feche as duas janelas para encerrar tudo.
echo   A extensao do navegador roda por conta propria (nao
echo   precisa deste script) e o Laragon/MySQL continua ligado.
echo ============================================
endlocal
