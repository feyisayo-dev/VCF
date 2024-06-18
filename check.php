<?php
// Path to the VCF file
$vcfFile = 'C:\xampp\htdocs\track\combined_merged.vcf';

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
$vcardIndex = 0;
$errors = [];

// Loop through each line and parse the vCard data
foreach ($lines as $line) {
    $line = trim($line);

    if ($line === 'BEGIN:VCARD') {
        if ($currentVCard !== null) {
            $errors[] = "vCard $vcardIndex: Missing END:VCARD";
        }
        $currentVCard = [];
        $vcardIndex++;
    } elseif ($line === 'END:VCARD') {
        if ($currentVCard === null) {
            $errors[] = "Unexpected END:VCARD at line $vcardIndex";
        } else {
            $vcards[] = $currentVCard;
            $currentVCard = null;
        }
    } elseif ($currentVCard !== null) {
        // Split the line into key and value
        $parts = explode(':', $line, 2);
        if (count($parts) === 2) {
            $key = $parts[0];
            $value = $parts[1];
            $currentVCard[$key] = $value;
        } else {
            $errors[] = "vCard $vcardIndex: Invalid line format - $line";
        }
    }
}

// Check for unclosed vCard
if ($currentVCard !== null) {
    $errors[] = "vCard $vcardIndex: Missing END:VCARD";
}

// Validate each vCard
foreach ($vcards as $index => $vcard) {
    if (!isset($vcard['VERSION'])) {
        $errors[] = "vCard " . ($index + 1) . ": Missing VERSION";
    }
    if (!isset($vcard['FN'])) {
        $errors[] = "vCard " . ($index + 1) . ": Missing FN";
    }
    if (!isset($vcard['N'])) {
        $errors[] = "vCard " . ($index + 1) . ": Missing N";
    }
}

// Output the results
if (empty($errors)) {
    echo "VCF file is valid.\n";
    echo "Number of vCards: " . count($vcards) . "\n";

} else {
    echo "VCF file has the following errors:\n";
    foreach ($errors as $error) {
        echo "- $error\n";
    }
}
?>
