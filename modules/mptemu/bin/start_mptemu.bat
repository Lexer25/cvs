@echo off
chcp 65001 >nul
rem MPT gate and tablo emulator launcher.
rem Panel: http://127.0.0.1:8080/cvs/mptemu
rem Stop: Ctrl+C
rem
rem Keep this file ASCII-only: cmd re-reads the batch file after chcp,
rem and non-ASCII bytes break line parsing.
cd /d "%~dp0..\..\.."
"C:\xampp\php\php.exe" index.php --task=emulator --port=8000
pause
