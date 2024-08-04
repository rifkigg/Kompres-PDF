<?php
function compressPdfWithGhostscript($inputFile, $compressionLevel) {
    $gsPath = '/usr/bin/gs';

    $compressionSettings = [
        'low' => '/screen',
        'medium' => '/ebook',
        'high' => '/prepress'
    ];

    $pdfSettings = $compressionSettings[$compressionLevel];
    $escapedInputFile = escapeshellarg($inputFile);
    $tempOutputFile = escapeshellarg($inputFile . ' ');

    $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=$pdfSettings -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$tempOutputFile $escapedInputFile";
    exec($command . ' 2>&1', $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("Ghostscript command failed: " . implode("\n", $output));
    }

    // Check file sizes before and after compression
    $originalSize = filesize($inputFile);
    $compressedSize = filesize($tempOutputFile);

    if ($compressedSize < $originalSize) {
        // Replace the original file with the compressed one if compression was successful
        rename($tempOutputFile, $inputFile);
        unlink($inputFile); // Delete the original file after successful compression
        return true; // Indicate that compression was successful
    } else {
        // Remove the temporary file if compression was not successful
        unlink($tempOutputFile);
        return false; // Indicate that compression was not successful
    }
}

function compressAllPdfsInDirectory($directory, $compressionLevel) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), 
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($files as $file) {
        if ($file->isFile() && strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)) === 'pdf') {
            $inputFile = $file->getRealPath();
            try {
                $success = compressPdfWithGhostscript($inputFile, $compressionLevel);
                if ($success) {
                    echo "Compressed and deleted: " . $inputFile . "\n";
                } else {
                    echo "Compression not successful: " . $inputFile . "\n";
                }
            } catch (Exception $e) {
                echo 'Error: ' . $e->getMessage() . "\n";
            }
        }
    }
}

$directoryToCompress = __DIR__; // Current directory
$compressionLevel = 'low'; // Default compression level, can be changed

compressAllPdfsInDirectory($directoryToCompress, $compressionLevel);
?>
