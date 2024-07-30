<?php
function compressPdfWithGhostscript($inputFile, $outputFile, $compressionLevel) {
    // Path ke executable Ghostscript di Windows
    $gsPath = 'C:\Program Files\gs\gs10.03.1\bin\gswin64c.exe'; // Ganti dengan path ke Ghostscript di sistem Anda

    // Mapping tingkat kompresi ke PDFSETTINGS Ghostscript
    $compressionSettings = [
        'low' => '/screen',
        'medium' => '/ebook',
        'high' => '/prepress'
    ];

    $pdfSettings = $compressionSettings[$compressionLevel];

    // Perintah untuk mengompres PDF menggunakan Ghostscript
    $command = "\"$gsPath\" -sDEVICE=pdfwrite -dCompatibilityLevel=1.4 -dPDFSETTINGS=$pdfSettings -dNOPAUSE -dQUIET -dBATCH -sOutputFile=\"$outputFile\" \"$inputFile\"";

    // Menjalankan perintah
    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        throw new Exception("Ghostscript command failed: " . implode("\n", $output));
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pdf'])) {
    $inputFile = $_FILES['pdf']['tmp_name'];
    $outputFile = 'compressed_' . $_FILES['pdf']['name'];
    $compressionLevel = $_POST['compression_level'];

    try {
        compressPdfWithGhostscript($inputFile, $outputFile, $compressionLevel);

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
        readfile($outputFile);
        unlink($outputFile); // Menghapus file setelah dikirim
    } catch (Exception $e) {
        echo 'Error: ' . $e->getMessage();
    }
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
                <label for="pdf" class="form-label mt-3">Pilih File PDF:</label>
                <input type="file" name="pdf" class="form-control" required>
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