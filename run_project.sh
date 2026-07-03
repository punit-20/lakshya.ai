#!/bin/bash
# LAKSHYA.AI - Termux/Linux Startup Wrapper Script

# Move to the directory containing this script
cd "$(dirname "$0")"

echo "Checking system requirements..."

# Verify Python installation
if command -v python3 &>/dev/null; then
    PYTHON_CMD="python3"
elif command -v python &>/dev/null; then
    PYTHON_CMD="python"
else
    echo "❌ Error: Python is not installed. Please run: pkg install python"
    exit 1
fi

# Verify PHP installation
if ! command -v php &>/dev/null; then
    echo "❌ Error: PHP is not installed. Please run: pkg install php"
    exit 1
fi

# Verify Composer installation
if ! command -v composer &>/dev/null; then
    echo "⚠️ Warning: Composer is not installed globally. Some Laravel operations might require it."
fi

# Verify Node.js/NPM installation
if ! command -v npm &>/dev/null; then
    echo "⚠️ Warning: NPM is not installed. Frontend assets compiled by Vite might not run."
fi

echo "All checks passed! Starting Lakshya.ai services via run_project.py..."
echo "------------------------------------------------------------"

$PYTHON_CMD run_project.py
