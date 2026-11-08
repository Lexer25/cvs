@echo off
setlocal

call :run sent_post.bat H946YE799 10
call :run sent_post.bat H025CO799 10
call :run sent_post.bat Y646MB977 10

call :run sent_post.bat H946YE799 7
call :run sent_post.bat H025CO799 7
call :run sent_post.bat Y646MB977 7
call :run sent_post.bat Y646MB977 7
call :run sent_post.bat Y646MB977 7
call :run sent_post.bat Y646MB977 7
call :run sent_post.bat Y646MB977 7

echo.
echo All done.
endlocal
exit /b

:run
call %*
timeout /t 1 /nobreak >nul
choice /c YN /n /m "Cotinue? [Y/N] "
if errorlevel 2 exit /b
goto :eof