@echo off
REM Starts the Keep-Shopping virtual try-on service on http://127.0.0.1:8001
cd /d "%~dp0"

if not exist ".venv\Scripts\python.exe" (
    echo Creating Python virtual environment...
    python -m venv .venv || goto :error
    ".venv\Scripts\python.exe" -m pip install --upgrade pip
    ".venv\Scripts\python.exe" -m pip install -r requirements.txt || goto :error
)

".venv\Scripts\python.exe" -m uvicorn app:app --host 127.0.0.1 --port 8001
goto :eof

:error
echo Setup failed. Make sure Python 3.10+ is installed and on PATH.
pause
