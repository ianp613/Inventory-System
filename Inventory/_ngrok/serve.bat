@echo off
setlocal

:: Read host from bot\config.json
for /f "delims=" %%i in ('powershell -NoProfile -Command ^
  "(Get-Content \"..\\bot\\config.json\" | ConvertFrom-Json).host"') do set host=%%i

:: Read port from bot\config.json
for /f "delims=" %%i in ('powershell -NoProfile -Command ^
  "(Get-Content \"..\\bot\\config.json\" | ConvertFrom-Json).port"') do set port=%%i

:: Confirm values
echo Host: %host%
echo Port: %port%

:: Start ngrok with the extracted host and port
start "" ngrok.exe http %host%:%port%

endlocal