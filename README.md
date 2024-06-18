VCF File Handling Scripts
This repository contains PHP scripts to handle VCF (vCard) files, including combining multiple VCF files, splitting a combined VCF file into smaller files, and merging duplicate vCards based on a common identifier.

Scripts Overview
1. combine_vcf.php
Description: Combines multiple VCF files into a single combined.vcf file.

Usage:

Place all VCF files to be combined in the vcf directory.
Run the script combine_vcf.php via CLI or web server.
Features:

Validates each VCF file format.
Handles quoted-printable encoding.
Outputs any errors encountered during processing.
2. split_vcf.php
Description: Splits a large combined.vcf file into smaller files, each containing up to 12,000 vCards.

Usage:

Ensure combined.vcf is present in the specified directory.
Run the script split_vcf.php via CLI or web server.
Features:

Splits the file into chunks based on vCard count.
Outputs each split file as combined_index.vcf.
3. merge_duplicates.php
Description: Merges duplicated vCards in a VCF file based on a common identifier (e.g., phone number).

Usage:

Ensure combined.vcf is present and contains vCards with identifiable information (e.g., phone numbers).
Run the script merge_duplicates.php via CLI or web server.
Features:

Identifies and merges vCards with the same identifier.
Outputs merged vCards as combined_merged.vcf.
Requirements
PHP (version 5.4 or higher recommended)
Web server (for web execution) or CLI environment
Example Use Case
If you have multiple VCF files from different sources and want to merge them into a single file for import into a phone or email client, you can use combine_vcf.php. After merging, you can split the large file into smaller chunks using split_vcf.php to comply with import limitations. If there are duplicate contacts due to overlaps between sources, merge_duplicates.php can clean up the file by consolidating duplicate entries.

License
This project is licensed under the MIT License - see the LICENSE file for details.

Acknowledgments
PHP community for valuable resources and libraries.
Open-source contributors for inspiration and guidance.
