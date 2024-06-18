<?php
// Path to the VCF file
$vcfFile = 'C:\xampp\htdocs\track\combined.vcf';

// Read the VCF file
$content = file_get_contents($vcfFile);

if ($content === false) {
    die('Failed to read VCF file.');
}

// Split the content by line
$lines = explode("\n", $content);

// Initialize an array to hold the vCard data
$vcards = [];
$currentVCard = null;

// Loop through each line and parse the vCard data
foreach ($lines as $line) {
    $line = trim($line);

    if ($line === 'BEGIN:VCARD') {
        $currentVCard = [];
    } elseif ($line === 'END:VCARD') {
        if ($currentVCard !== null) {
            $vcards[] = $currentVCard;
            $currentVCard = null;
        }
    } elseif ($currentVCard !== null) {
        // Split the line into key and value
        $parts = explode(':', $line, 2);
        if (count($parts) === 2) {
            $key = $parts[0];
            $value = $parts[1];

            // Store the key-value pair in the current vCard
            if (!isset($currentVCard[$key])) {
                $currentVCard[$key] = $value;
            } else {
                // Handle multiple occurrences of the same key
                if (!is_array($currentVCard[$key])) {
                    $currentVCard[$key] = [$currentVCard[$key]];
                }
                $currentVCard[$key][] = $value;
            }
        }
    }
}

// Start HTML output
echo "<!DOCTYPE html>
<html>
<head>
    <title>VCF Information</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
<h2>VCF Information</h2>";

// Display the vCard data in a table
foreach ($vcards as $index => $vcard) {
    echo "<h3>vCard " . ($index + 1) . "</h3>";
    echo "<table>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    foreach ($vcard as $key => $value) {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        echo "<tr><td>$key</td><td>$value</td></tr>";
    }
    echo "</table><br>";
}

// End HTML output
echo "</body>
</html>";
?>
