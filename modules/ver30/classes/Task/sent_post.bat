@echo off
setlocal

set URL=http://127.0.0.1:8080/cvs/dashboard/exec

set PLATE=%~1
set CAMERA=%~2

C:\xampp\apache\bin\curl.exe -i -X POST "%URL%" ^
  -H "Content-Type: application/json" ^
  -H "Accept: application/json" ^
  --data-raw "{\"plate\":{\"camera\":\"%CAMERA%\",\"channel\":3,\"count\":16,\"dateTime\":\"20250429T141918Z\",\"description\":\"---\",\"direction\":0,\"groupId\":-1,\"id\":624101,\"image\":\"\",\"inList\":0,\"passed\":1,\"plate\":\"%PLATE%\",\"quality\":\"555555555000\",\"stayTimeMinutes\":0,\"type\":0,\"weight\":0}}"

echo.
echo Done.
endlocal