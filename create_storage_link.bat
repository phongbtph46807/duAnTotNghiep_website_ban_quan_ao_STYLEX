@echo off
cd /d "%~dp0"
if exist "public\storage" rmdir "public\storage"
mklink /D "public\storage" "%~dp0storage\app\public"
echo Storage link created successfully!
pause