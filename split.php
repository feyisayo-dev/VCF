<?php
// Path to the combined VCF file
$combinedFile = 'C:\xampp\htdocs\track\combined_merged.vcf';

// Maximum number of vCards per file
$maxCardsPerFile = 12000;

// Read the combined VCF file
$content = file_get_contents($combinedFile);

if ($content === false) {
    die("Failed to read combined VCF file: $combinedFile");
}

// Split the content by vCards
$vcards = explode("END:VCARD", $content);

// Initialize variables
$fileIndex = 1;
$cardCount = 0;
$chunkIndex = 1;

// Loop through vCards and split into chunks
foreach ($vcards as $vcard) {
    // Skip empty entries (usually last element after explode)
    if (trim($vcard) === '') {
        continue;
    }

    // Add back END:VCARD which was removed by explode
    $vcard .= "END:VCARD";

    // Append current vCard to chunk
    $currentChunk .= $vcard . "\n";

    // Increment card count
    $cardCount++;

    // If reached max cards per file, write current chunk to file
    if ($cardCount >= $maxCardsPerFile) {
        $outputFile = "C:\\xampp\\htdocs\\track\\split\\combined_$fileIndex.vcf";
        if (file_put_contents($outputFile, $currentChunk) !== false) {
            echo "Created split VCF file: $outputFile\n";
        } else {
            echo "Failed to write split VCF file: $outputFile\n";
        }

        // Reset variables for next chunk
        $fileIndex++;
        $cardCount = 0;
        $currentChunk = '';
    }
}

// Write the last chunk if not already written (for remaining vCards)
if (!empty($currentChunk)) {
    $outputFile = "C:\\xampp\\htdocs\\track\\split\\combined_$fileIndex.vcf";
    if (file_put_contents($outputFile, $currentChunk) !== false) {
        echo "Created split VCF file: $outputFile\n";
    } else {
        echo "Failed to write split VCF file: $outputFile\n";
    }
}

echo "Splitting of VCF file completed.\n";
?>
