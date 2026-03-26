param(
    [string]$Version = "dev"
)

$ErrorActionPreference = "Stop"

$projectRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$themeName = Split-Path -Leaf $projectRoot
$tempRoot = Join-Path $projectRoot ".build-tmp"
$tempThemeDir = Join-Path $tempRoot $themeName
$outputZip = Join-Path $projectRoot ("{0}-{1}.zip" -f $themeName, $Version)

$excludePatterns = @(
    ".git",
    ".vscode",
    "*.zip",
    "*.md",
    "build-theme-zip.ps1",
    ".build-tmp"
)

if (Test-Path $tempRoot) {
    Remove-Item $tempRoot -Recurse -Force
}

New-Item -ItemType Directory -Path $tempThemeDir -Force | Out-Null

Get-ChildItem -Path $projectRoot -Force | Where-Object {
    $name = $_.Name
    -not ($excludePatterns | Where-Object { $name -like $_ })
} | ForEach-Object {
    Copy-Item -Path $_.FullName -Destination $tempThemeDir -Recurse -Force
}

if (Test-Path $outputZip) {
    Remove-Item $outputZip -Force
}

Compress-Archive -Path $tempThemeDir -DestinationPath $outputZip -CompressionLevel Optimal
Remove-Item $tempRoot -Recurse -Force

Write-Host "ZIP gerado com sucesso: $outputZip"
