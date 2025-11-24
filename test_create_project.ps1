$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiI2OTIzYTg3OTIyZmE2Ni4xNjg1MjQ3OCIsImVtYWlsIjoiYWRtaW5AZ290cmliZS5jbyIsInJvbGUiOiJDUkVBVE9SIiwiaWF0IjoxNzY0MDA2NjIwLCJleHAiOjE3NjQwMTAyMjB9.IewJA2G3e98vahg7VAXFRIMCoQzKoeAuTQV4dTz9iLQ"

$headers = @{
    "Authorization" = "Bearer $token"
    "Content-Type" = "application/json"
}

$body = @{
    title = "Aventura de Senderismo"
    description = "Caminata por las montañas"
    activityType = "HIKING"
    startDateTime = "2024-12-15T08:00:00"
    meetingPoint = "Parque Nacional"
    price = 50000
    currency = "COP"
    maxGuests = 15
} | ConvertTo-Json

try {
    $response = Invoke-WebRequest -Uri "https://gotribe.co/api/projects" -Method POST -Headers $headers -Body $body -UseBasicParsing
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Response: $($response.Content)"
} catch {
    Write-Host "Status: $($_.Exception.Response.StatusCode.value__)"
    $reader = [System.IO.StreamReader]::new($_.Exception.Response.GetResponseStream())
    Write-Host "Error: $($reader.ReadToEnd())"
}
