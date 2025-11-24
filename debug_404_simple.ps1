try {
    $response = Invoke-WebRequest -Uri "https://gotribe.co/api/participations" -Method GET -UseBasicParsing
    Write-Host "Success: $($response.Content)"
}
catch {
    $stream = $_.Exception.Response.GetResponseStream()
    $reader = [System.IO.StreamReader]::new($stream)
    $content = $reader.ReadToEnd()
    Write-Host "Error Content: $content"
}
