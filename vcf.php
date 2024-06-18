<?php
// Directory containing VCF files
$directory = 'C:\xampp\htdocs\track\vcf';
$outputFile = 'C:\xampp\htdocs\track\combined.vcf';

// Get all VCF files in the directory
$vcfFiles = glob($directory . '/*.vcf');

if (!$vcfFiles) {
    die('No VCF files found in the directory.');
}

$combinedContent = '';

// Function to decode Quoted-Printable strings
function decode_quoted_printable($string) {
    return quoted_printable_decode($string);
}

// Function to clean and validate a single VCF file
function clean_vcf($content, &$vcardIndex) {
    $lines = explode("\n", $content);
    $cleanedContent = '';
    $currentVCard = null;
    $errors = [];
    $currentVCardFields = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === 'BEGIN:VCARD') {
            if ($currentVCard !== null) {
                $errors[] = "vCard $vcardIndex: Missing END:VCARD";
            }
            $currentVCard = "BEGIN:VCARD\n";
            $vcardIndex++;
        } elseif ($line === 'END:VCARD') {
            if ($currentVCard === null) {
                $errors[] = "Unexpected END:VCARD at line $vcardIndex";
            } else {
                // Ensure FN and N fields are present
                if (!isset($currentVCardFields['FN'])) {
                    $currentVCard .= "FN:official_$vcardIndex\n";
                }
                if (!isset($currentVCardFields['N'])) {
                    $currentVCard .= "N:official_$vcardIndex;;;;\n";
                }
                $currentVCard .= "END:VCARD\n";
                $cleanedContent .= $currentVCard;
                $currentVCard = null;
                $currentVCardFields = [];
            }
        } elseif ($currentVCard !== null) {
            // Split the line into key and value
            $parts = explode(':', $line, 2);
            if (count($parts) === 2) {
                $key = $parts[0];
                $value = $parts[1];
                // Handle special characters or emojis in value
                if (strtolower($key) === 'fn' || strtolower($key) === 'n') {
                    // If the key is FN or N, handle the value with quoted printable decode
                    $decodedValue = decode_quoted_printable($value);
                } else {
                    // For other keys, use the value as-is
                    $decodedValue = $value;
                }
                $currentVCardFields[$key] = $decodedValue;
                $currentVCard .= "$key:$decodedValue\n";
            } else {
                $errors[] = "vCard $vcardIndex: Invalid line format - $line";
            }
        } else {
            $errors[] = "Line outside of vCard - $line";
        }
    }

    // Check for unclosed vCard
    if ($currentVCard !== null) {
        $errors[] = "vCard $vcardIndex: Missing END:VCARD";
    }

    // Return cleaned content and errors
    return [$cleanedContent, $errors];
}


$allErrors = [];
$vcardIndex = 0;

// Loop through each VCF file, clean it, and append its content to the combinedContent variable
foreach ($vcfFiles as $vcfFile) {
    $content = file_get_contents($vcfFile);
    if ($content !== false) {
        list($cleanedContent, $errors) = clean_vcf($content, $vcardIndex);
        $combinedContent .= $cleanedContent;
        $allErrors = array_merge($allErrors, $errors);
    } else {
        echo "Failed to read file: $vcfFile\n";
    }
}

// Write the combined content to the output file
if (file_put_contents($outputFile, $combinedContent) !== false) {
    echo "Combined VCF file created successfully at: $outputFile\n";
} else {
    echo "Failed to write combined VCF file.\n";
}

// Output any errors found
if (!empty($allErrors)) {
    echo "The following errors were found:\n";
    foreach ($allErrors as $error) {
        echo "- $error\n";
    }
} else {
    echo "No errors found.\n";
}
?>
