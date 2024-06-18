<?php
// Path to the combined VCF file
$combinedFile = 'C:\xampp\htdocs\track\combined.vcf';
// Output file for merged vCards
$outputFile = 'C:\xampp\htdocs\track\combined_merged.vcf';

// Read the combined VCF file
$content = file_get_contents($combinedFile);

if ($content === false) {
    die("Failed to read combined VCF file: $combinedFile");
}

// Split the content by vCards
$vcards = explode("END:VCARD", $content);

// Initialize an array to store vCard information by identifier
$vcardsData = [];

// Function to parse vCard content and extract identifier (phone number in this case)
function parse_vcard($vcard) {
    preg_match('/TEL:(.*?)[;\r\n]/', $vcard, $matches);
    if (isset($matches[1])) {
        return $matches[1];
    } else {
        return null;
    }
}

// Process each vCard
foreach ($vcards as $vcard) {
    // Skip empty entries (usually last element after explode)
    if (trim($vcard) === '') {
        continue;
    }

    // Add back END:VCARD which was removed by explode
    $vcard .= "END:VCARD";

    // Parse vCard to get identifier (phone number)
    $identifier = parse_vcard($vcard);

    if ($identifier === null) {
        continue; // Skip vCard if no identifier found
    }

    // Store vCard content based on identifier
    if (!isset($vcardsData[$identifier])) {
        $vcardsData[$identifier] = $vcard . "\n";
    } else {
        // Merge the vCard content if identifier already exists
        // Example: append additional fields or decide how to merge
        // Here, we are simply concatenating the vCard content
        $vcardsData[$identifier] .= $vcard . "\n";
    }
}

// Write merged vCards to output file
$mergedContent = implode("\n", $vcardsData);

if (file_put_contents($outputFile, $mergedContent) !== false) {
    echo "Merged VCF file created successfully at: $outputFile\n";
} else {
    echo "Failed to write merged VCF file.\n";
}
