<?php
$zip = new ZipArchive;
if ($zip->open('vendor.zip') === TRUE) {
    $zip->extractTo('./');
    $zip->close();
    echo 'Unzipped successfully!';
} else {
    echo 'Unzip failed';
}
?>
