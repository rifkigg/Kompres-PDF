<h1><strong>Langkah - Langkah Menggunakan Program Ini</strong></h1>
<h1>Pertama</h1> 
harus install GhostScript, dibawah ini adalah tutorialnya
<h3>Windows</h3>
Unduh Ghostscript:
<ol>
  <li>Kunjungi situs resmi Ghostscript.</li>
    Pilih versi Windows dan unduh file instalasi.
  <li>Instal Ghostscript:</li>
    Jalankan file instalasi yang telah diunduh.
    Ikuti petunjuk instalasi hingga selesai.
<li>Verifikasi Instalasi:</li>
    Buka Command Prompt.
    Ketik gswin64c -v dan tekan Enter.
    Jika terinstal dengan benar, Anda akan melihat informasi versi Ghostscript.
</ol>

<h3>macOS</h3>
Menggunakan Homebrew:
<ol>
  <li>Buka Terminal.</li>
    Ketik brew install ghostscript dan tekan Enter.
  <li>Verifikasi Instalasi:</li>
    Ketik gs -v di Terminal dan tekan Enter.
    Jika terinstal dengan benar, Anda akan melihat informasi versi Ghostscript.
</ol>

<h3>Linux</h3>
<ol>
<li>Debian/Ubuntu:</li>
Buka Terminal.
Ketik "sudo apt-get update" (tanpa menggunakan tanda petik) dan tekan Enter.
Ketik "sudo apt-get install" (tanpa menggunakan tanda petik) ghostscript dan tekan Enter.
<li>Fedora:</li>
Buka Terminal.
Ketik sudo dnf install ghostscript dan tekan Enter.
<li>Verifikasi Instalasi:</li>
Ketik gs -v di Terminal dan tekan Enter.
Jika terinstal dengan benar, Anda akan melihat informasi versi Ghostscript.
</ol>

<h1><strong>Kedua</strong></h1>
Lalu sesuaikan path GhostScript pada variable sgPath
lalu jalankan
