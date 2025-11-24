try {
    $response = Invoke-WebRequest -Uri "https://gotribe.co/api/participations" -Method GET -UseBasicParsing
    Write-Host "Success: $($response.Content)"
}
catch {
    $stream = $_.Exception.Response.GetResponseStream()
    $reader = [System.IO.StreamReader]::new($stream)
    $content = $reader.ReadToEnd()
    
    # Try to parse JSON and extract checked_path
    try {
        $json = $content | ConvertFrom-Json
        Write-Host "CHECKED_PATH: $($json.checked_path)"
        Write-Host "FULL_JSON: $content"
    }
    catch {
        Write-Host "Could not parse JSON. Raw content:"
        Write-Host $content
    }
}
