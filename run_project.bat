@echo off
title Lakshya.ai Unified Project Runner
cd /d "%~dp0"

echo Checking system requirements...

where python >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Error: Python is not installed or not in PATH.
    echo Please install Python and make sure it is in your PATH, then try again.
    pause
    exit /b 1
)

echo Starting Lakshya.ai services via run_project.py...
echo ------------------------------------------------------------
(
  python run_project.py
) < nul

if %errorlevel% neq 0 (
    echo Project runner exited with error code %errorlevel%.
    pause
)

