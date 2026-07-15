$path = "C:\own\wp_bc\wp-content\themes\generatepress-child\style-compiled.css"
$c = [System.IO.File]::ReadAllText($path)

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-glossary-nav-link{align-items:center;background:#2d5a27;border:1px solid #2d5a27;border-radius:4px;color:#f5f0eb;cursor:pointer;display:inline-flex;font-size:.85rem;font-weight:600;height:2.2rem;justify-content:center;line-height:1;padding:0;text-decoration:none;transition:background .15s,border-color .15s,color .15s;width:2.2rem}"), ".bc-glossary-archive .bc-glossary-nav-link{align-items:center;background:#2e7d32;border:1px solid #2e7d32;border-radius:4px;color:#fff;cursor:pointer;display:inline-flex;font-size:.85rem;font-weight:600;height:2.2rem;justify-content:center;line-height:1;padding:0;text-decoration:none;transition:background .15s,border-color .15s,color .15s;width:2.2rem}"

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-glossary-nav-link:hover{background:#3d7a37;border-color:#3d7a37;color:#fff}"), ".bc-glossary-archive .bc-glossary-nav-link:hover{background:#388e3c;border-color:#388e3c;color:#fff}"

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-glossary-nav-link.active,.bc-glossary-archive .bc-glossary-nav-link.active:hover{background:#4a3728;border-color:#4a3728;color:#fff}.bc-glossary-archive .bc-glossary-nav-link.active:hover{background:#5c4736;border-color:#5c4736}"), ".bc-glossary-archive .bc-glossary-nav-link.active,.bc-glossary-archive .bc-glossary-nav-link.active:hover{background:#e65100;border-color:#e65100;color:#fff}.bc-glossary-archive .bc-glossary-nav-link.active:hover{background:#f57c00;border-color:#f57c00}"

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:hover,.bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:focus{color:#2d5a27;text-decoration:underline}"), ".bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:hover,.bc-glossary-archive .bc-glossary-letter-group .bc-glossary-entry-link:focus{color:#e65100;text-decoration:underline}"

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-glossary-letter-heading{border-bottom:1px solid #ddd;color:#1e3a5f;font-family:Merriweather,Georgia,Times New Roman,serif;font-size:1.5rem;font-weight:700;margin-bottom:.75rem;padding-bottom:.25rem}"), ".bc-glossary-archive .bc-glossary-letter-heading{border-bottom:2px solid #2e7d32;color:#2e7d32;font-family:Merriweather,Georgia,Times New Roman,serif;font-size:1.5rem;font-weight:700;margin-bottom:.75rem;padding-bottom:.25rem}"

$c = $c -replace [regex]::Escape(".bc-glossary-archive .bc-filter-input:focus{border-color:#2d5a27;outline:none;box-shadow:0 0 0 2px rgba(45,90,39,.2)}"), ".bc-glossary-archive .bc-filter-input:focus{border-color:#2e7d32;outline:none;box-shadow:0 0 0 2px rgba(46,125,50,.2)}"

[System.IO.File]::WriteAllText($path, $c)
Write-Host "Done"
