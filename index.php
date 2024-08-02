<?php
function compressPdfWithGhostscript($inputFile, $outputFile, $compressionLevel) {
    $gsPath = '/usr/bin/gs';

    $compressionSettings = [
        'low' => '/screen',
        'medium' => '/ebook',
        'high' => '/prepress'
    ];

    $pdfSettings = $compressionSettings[$compressionLevel];
    $escapedInputFile = escapeshellarg($inputFile);
    $escapedOutputFile = escapeshellarg($outputFile);

    $command = "$gsPath -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=$pdfSettings -dNOPAUSE -dQUIET -dBATCH -sOutputFile=$escapedOutputFile $escapedInputFile";
    exec($command . ' 2>&1', $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("Ghostscript command failed: " . implode("\n", $output));
    }
}

function createZipFromDirectory($directory, $outputZip) {
    $zip = new ZipArchive();
    if ($zip->open($outputZip, ZipArchive::CREATE) !== TRUE) {
        throw new Exception("Cannot create zip file");
    }

    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $file) {
        if ($file->isFile()) {
            $filePath = $file->getRealPath();
            $relativePath = basename($filePath);
            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();
}

function deleteDirectory($dir) {
    if (!is_dir($dir)) {
        return;
    }
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($files as $fileinfo) {
        $todo = ($fileinfo->isDir() ? 'rmdir' : 'unlink');
        $todo($fileinfo->getRealPath());
    }
    rmdir($dir);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['archive'])) {
    $compressionLevel = $_POST['compression_level'];
    $archivePath = $_FILES['archive']['tmp_name'];
    $extractPath = 'extracted';
    $compressedPath = 'compressed_pdfs';

    // Create directories for extraction and compressed files
    if (!file_exists($extractPath)) {
        mkdir($extractPath, 0777, true);
    }
    if (!file_exists($compressedPath)) {
        mkdir($compressedPath, 0777, true);
    }

    // Extract the zip or rar archive
    $zip = new ZipArchive();
    if ($zip->open($archivePath) === TRUE) {
        $zip->extractTo($extractPath);
        $zip->close();
    } else {
        throw new Exception("Failed to open zip file");
    }

    // Compress each PDF file in the extracted directory
    $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($extractPath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::LEAVES_ONLY);
    foreach ($files as $file) {
        if ($file->isFile() && strtolower(pathinfo($file->getFilename(), PATHINFO_EXTENSION)) === 'pdf') {
            $inputFile = $file->getRealPath();
            $outputFile = $compressedPath . '/compressed_' . $file->getFilename();
            compressPdfWithGhostscript($inputFile, $outputFile, $compressionLevel);
        }
    }

    // Create a zip file from the compressed PDFs folder
    $finalZipPath = 'final_compressed_pdfs.zip';
    createZipFromDirectory($compressedPath, $finalZipPath);

    // Serve the final zip file to the user
    header('Content-Type: application/zip');
    header('Content-Disposition: attachment; filename="' . basename($finalZipPath) . '"');
    readfile($finalZipPath);

    // Clean up temporary files
    deleteDirectory($extractPath);
    deleteDirectory($compressedPath);
    unlink($finalZipPath);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Compress PDF</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .full-height {
            height: 100vh;
        }
    </style>
</head>
<body class="bg-secondary">
    <div class="container d-flex flex-column gap-5 justify-content-center align-items-center full-height ">
        <div class="bg-light rounded p-5">
            <h1>Upload PDF Untuk di Kompress</h1>
            <form method="post" enctype="multipart/form-data">
                <label for="archive" class="form-label mt-3">Pilih File ZIP:</label>
                <input type="file" name="archive" class="form-control" required>
                <label for="compression_level" class="form-label mt-3">Pilih Tingkat Kompresi:</label>
                <select name="compression_level" class="form-control" required>
                    <option value="low">Low | Rendah</option>
                    <option value="medium">Medium | Sedang</option>
                    <option value="high">High | Tinggi</option>
                </select>
                <input type="submit" value="Compress PDF" class="btn btn-primary mt-3 w-100">
            </form>
        </div>
        <h6>Copyright @ 2024 - M Rifki Adi Setiawan - Coded with ❤️</h6>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
