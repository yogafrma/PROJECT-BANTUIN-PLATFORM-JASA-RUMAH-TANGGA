Add-Type -AssemblyName System.Drawing

$src = [System.Drawing.Bitmap]::FromFile("C:\Users\HYPE AMD\.gemini\antigravity-ide\brain\8c7de81b-7c9f-45f8-9b85-6504853fa099\.user_uploaded\media_1789176985130.jpg")

# Find the exact bounds of the circular emblem:
# The circle is located in the upper half of the image.
# Let's scan from Y=10 to 445, X=0 to 1023
$minX = 1000; $maxX = 0; $minY = 1000; $maxY = 0

for ($y = 10; $y -lt 445; $y++) {
    for ($x = 0; $x -lt $src.Width; $x++) {
        $p = $src.GetPixel($x, $y)
        # Check against background (249, 249, 247)
        $diff = [Math]::Abs($p.R - 249) + [Math]::Abs($p.G - 249) + [Math]::Abs($p.B - 247)
        if ($diff -gt 25) {
            if ($x -lt $minX) { $minX = $x }
            if ($x -gt $maxX) { $maxX = $x }
            if ($y -lt $minY) { $minY = $y }
            if ($y -gt $maxY) { $maxY = $y }
        }
    }
}

Write-Output "Emblem Exact Bounds: minX=$minX, maxX=$maxX, minY=$minY, maxY=$maxY"
Write-Output "Emblem Width=$($maxX-$minX+1), Height=$($maxY-$minY+1)"

# Now find exact bounds of "BANTU IN" text (Y=445 to 550)
$tMinX = 1000; $tMaxX = 0; $tMinY = 1000; $tMaxY = 0
for ($y = 445; $y -lt 550; $y++) {
    for ($x = 0; $x -lt $src.Width; $x++) {
        $p = $src.GetPixel($x, $y)
        $diff = [Math]::Abs($p.R - 249) + [Math]::Abs($p.G - 249) + [Math]::Abs($p.B - 247)
        if ($diff -gt 25) {
            if ($x -lt $tMinX) { $tMinX = $x }
            if ($x -gt $tMaxX) { $tMaxX = $x }
            if ($y -lt $tMinY) { $tMinY = $y }
            if ($y -gt $tMaxY) { $tMaxY = $y }
        }
    }
}
Write-Output "Text 'BANTU IN' Bounds: minX=$tMinX, maxX=$tMaxX, minY=$tMinY, maxY=$tMaxY"
Write-Output "Text Width=$($tMaxX-$tMinX+1), Height=$($tMaxY-$tMinY+1)"

# Subtitle "Solusi Cepat & Ahli Kebutuhan Rumah" (Y=550 to 638)
$sMinX = 1000; $sMaxX = 0; $sMinY = 1000; $sMaxY = 0
for ($y = 550; $y -lt $src.Height; $y++) {
    for ($x = 0; $x -lt $src.Width; $x++) {
        $p = $src.GetPixel($x, $y)
        $diff = [Math]::Abs($p.R - 249) + [Math]::Abs($p.G - 249) + [Math]::Abs($p.B - 247)
        if ($diff -gt 25) {
            if ($x -lt $sMinX) { $sMinX = $x }
            if ($x -gt $sMaxX) { $sMaxX = $x }
            if ($y -lt $sMinY) { $sMinY = $y }
            if ($y -gt $sMaxY) { $sMaxY = $y }
        }
    }
}
Write-Output "Subtitle Bounds: minX=$sMinX, maxX=$sMaxX, minY=$sMinY, maxY=$sMaxY"
Write-Output "Subtitle Width=$($sMaxX-$sMinX+1), Height=$($sMaxY-$sMinY+1)"

$src.Dispose()
