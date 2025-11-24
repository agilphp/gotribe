# Script de Validación Completa GoTribe API
$ErrorActionPreference = "Stop"

function Test-Endpoint {
    param($Uri, $Method, $Headers, $Body)
    Write-Host "Testing $Method $Uri..." -ForegroundColor Cyan
    try {
        if ($Body) {
            $response = Invoke-WebRequest -Uri $Uri -Method $Method -Headers $Headers -Body $Body -UseBasicParsing -ContentType "application/json"
        }
        else {
            $response = Invoke-WebRequest -Uri $Uri -Method $Method -Headers $Headers -UseBasicParsing -ContentType "application/json"
        }
        Write-Host "SUCCESS ($($response.StatusCode))" -ForegroundColor Green
        return $response.Content | ConvertFrom-Json
    }
    catch {
        Write-Host "FAILED ($($_.Exception.Response.StatusCode.value__))" -ForegroundColor Red
        $reader = [System.IO.StreamReader]::new($_.Exception.Response.GetResponseStream())
        Write-Host "Error Details: $($reader.ReadToEnd())" -ForegroundColor Yellow
        return $null
    }
}

# 1. Login
$loginBody = @{
    email    = "admin@gotribe.co"
    password = "Ea94490X*ASD2025*"
} | ConvertTo-Json

$authData = Test-Endpoint -Uri "https://gotribe.co/api/auth/login" -Method "POST" -Body $loginBody
if (-not $authData) { exit }

$token = $authData.token
Write-Host "Token obtained: $token" -ForegroundColor Gray

$headers = @{
    "Authorization" = "Bearer $token"
}

# 2. Create Project
$projectBody = @{
    title         = "Aventura de Senderismo PowerShell"
    description   = "Prueba desde script de validacion"
    activityType  = "HIKING"
    startDateTime = "2024-12-15T08:00:00"
    meetingPoint  = "Parque Nacional"
    price         = 50000
    currency      = "COP"
    maxGuests     = 15
} | ConvertTo-Json

$projectData = Test-Endpoint -Uri "https://gotribe.co/api/projects" -Method "POST" -Headers $headers -Body $projectBody

if ($projectData) {
    Write-Host "Project Created ID: $($projectData.id)" -ForegroundColor Green
}
