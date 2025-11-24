try { 
    $response = Invoke-WebRequest -Uri 'https://gotribe.co/api/participations' -Method GET -UseBasicParsing
    Write-Host "Status: $($response.StatusCode)"
    Write-Host "Content: $($response.Content)"
}
catch { 
    $e = $_.Exception
    Write-Host "Status: $($e.Response.StatusCode.value__)"
    if ($e.Response) {
        $reader = [System.IO.StreamReader]::new($e.Response.GetResponseStream())
        Write-Host "Error Content: $($reader.ReadToEnd())"
    }
    else {
        Write-Host "Error Message: $($e.Message)"
    }
}
