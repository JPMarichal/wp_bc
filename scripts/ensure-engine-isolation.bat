@echo off
:: Engine Isolation - Startup Fix Wrapper
:: Runs the PowerShell script that ensures Rancher Desktop and Podman Desktop remain isolated.

powershell -ExecutionPolicy Bypass -NoLogo -NoProfile -File "%~dp0scripts\ensure-engine-isolation.ps1" %*
