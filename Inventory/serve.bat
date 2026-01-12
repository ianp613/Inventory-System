@echo off
title (DON'T CLOSE) - Wifi Team Inventory
color 0A

:: Get host input or fallback to local IP
set /p host=Enter Host: 
if "%host%"=="" for /f "tokens=2 delims=:" %%i in ('ipconfig ^| findstr /i "IPv4"') do (
    for /f "tokens=1 delims= " %%a in ("%%i") do set host=%%a
)

:: Get port input
set /p port=Enter Port: 
if "%port%"=="" set port=8552

echo.
echo System will be running in http://%host%:%port%
pause

:: Save host and port to bot\config.json
(
    echo {
    echo     "host": "%host%",
    echo     "port": "%port%"
    echo }
) > "%~dp0bot\config.json"

:: run telegram bot pyton
start "" cmd /c "cd /d %~dp0bot && serve.bat"
start "" cmd /c "cd /d %~dp0_ngrok && serve.bat"

:: start msedge http://%host%:%port%
php -S %host%:%port%