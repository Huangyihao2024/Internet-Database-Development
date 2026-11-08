@echo off
cd /d "%~dp0"
python scripts/oneclick.py
if errorlevel 1 echo Deployment failed. Review the message above; existing databases were not overwritten.
pause
