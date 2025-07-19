<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Hasil Penilaian Kuesioner</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #000;
            color: #00ff00;
            font-family: 'Courier New', monospace;
        }

        a,
        .text-dark {
            color: #00ff00 !important;
        }

        .card,
        .alert {
            background-color: #111 !important;
            border-color: #0f0 !important;
            color: #00ff00 !important;
        }


        .table th,
        .table td {
            color: #0f0;
            background-color: #000 !important;
        }

        .table-dark {
            background-color: #003300 !important;
        }

        #loadingScreen {
            position: fixed;
            inset: 0;
            background-color: #000;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            color: #00ff00;
        }

        #matrixCanvas {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 0;
            width: 100%;
            height: 100%;
            background-color: black;
            pointer-events: none;
            opacity: 0.15;
        }


        .hidden {
            display: none !important;
        }

        #scanOutput p {
            animation: typing 0.6s steps(40) forwards;
            white-space: nowrap;
            overflow: hidden;
            border-right: 2px solid #00ff00;
            width: 0;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <!-- Efek Matriks -->
    <canvas id="matrixCanvas"></canvas>

    <!-- Loading Matrix Style -->
    <div id="loadingScreen">
        <h2 class="mb-3">[🟢 ANALYZING SECURED CSV FILE...]</h2>
        <div id="scanOutput" class="text-start" style="max-width: 600px;">
            <p>> Accessing Data Stream...</p>
            <p>> Authenticating Responden...</p>
            <p>> Parsing Jawaban...</p>
            <p>> Compiling Statistical Output...</p>
            <p>> Rendering Final Report...</p>
        </div>
    </div>

    <!-- Container hasil disembunyikan dulu -->
    <div id="hasilContainer" class="container my-5 hidden">

        <?php
        if (isset($_FILES['file_csv']) && $_FILES['file_csv']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['file_csv']['tmp_name'];

            $nilaiMap = [
                "Sangat Setuju" => 5,
                "Setuju" => 4,
                "Cukup" => 3,
                "Tidak Setuju" => 2,
                "Sangat Tidak Setuju" => 1
            ];

            $kategori = array_keys($nilaiMap);
            $analisis_per_soal = [];

            if (($handle = fopen($fileTmpPath, "r")) !== FALSE) {
                $header = fgetcsv($handle);
                $jumlah_soal = count($header) - 5;

                for ($i = 0; $i < $jumlah_soal; $i++) {
                    foreach ($kategori as $k) {
                        $analisis_per_soal[$i][$k] = 0;
                    }
                }

                echo '<div class="card mb-4 border-success">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0">🔎 Nilai Per Responden</h5>
                </div>
                <div class="card-body table-responsive">';
                echo "<table class='table table-bordered table-striped'>";
                echo "<thead class='table-dark'><tr><th>No</th><th>Nama</th><th>Waktu</th>";
                for ($i = 0; $i < $jumlah_soal; $i++) {
                    echo "<th>S" . ($i + 1) . "</th>";
                }
                echo "<th>Total</th><th>Rata-rata</th></tr></thead><tbody>";

                $total_nilai_keseluruhan = 0;
                $jumlah_responden = 0;

                while (($data = fgetcsv($handle)) !== FALSE) {
                    $jumlah_responden++;
                    $timestamp = $data[0];
                    $nama = trim($data[2]);

                    $total_nilai = 0;
                    $jumlah_pertanyaan = 0;

                    echo "<tr><td>{$jumlah_responden}</td><td>{$nama}</td><td>{$timestamp}</td>";

                    for ($i = 0; $i < $jumlah_soal; $i++) {
                        $kolom = 5 + $i;
                        $jawaban = ucwords(strtolower(trim($data[$kolom])));

                        $nilai = isset($nilaiMap[$jawaban]) ? $nilaiMap[$jawaban] : 0;
                        echo "<td><strong>{$nilai}</strong></td>";

                        if ($nilai > 0) {
                            $total_nilai += $nilai;
                            $jumlah_pertanyaan++;
                            $analisis_per_soal[$i][$jawaban]++;
                        }
                    }

                    $rata_rata = $jumlah_pertanyaan > 0 ? round($total_nilai / $jumlah_pertanyaan, 2) : 0;
                    $total_nilai_keseluruhan += $total_nilai;

                    echo "<td><strong>{$total_nilai}</strong></td><td><strong>{$rata_rata}</strong></td></tr>";
                }


                echo "</tbody></table></div></div>";

                $rata_rata_keseluruhan = $jumlah_responden > 0 ? round($total_nilai_keseluruhan / $jumlah_responden, 2) : 0;

                echo '<div class="alert alert-success border-success">
                <h5 class="mb-3">📊 Rekap Keseluruhan</h5>
                <ul class="mb-0">
                    <li><strong>Jumlah Responden:</strong> ' . $jumlah_responden . '</li>
                    <li><strong>Total Nilai Keseluruhan:</strong> ' . $total_nilai_keseluruhan . '</li>
                    <li><strong>Rata-rata Nilai Responden:</strong> ' . $rata_rata_keseluruhan . '</li>
                </ul>
              </div>';

                echo '<div class="card border-success">
                <div class="card-header bg-success text-black">
                    <h5 class="mb-0">📋 Analisis Jawaban Per Soal</h5>
                </div>
                <div class="card-body">';

                for ($i = 0; $i < $jumlah_soal; $i++) {
                    echo "<h6 class='fw-bold text-success'>Soal " . ($i + 1) . "</h6><ul>";
                    foreach ($kategori as $k) {
                        echo "<li>$k: <strong>{$analisis_per_soal[$i][$k]}</strong></li>";
                    }
                    echo "</ul><hr>";
                }

                echo "</div></div>";

                fclose($handle);
            } else {
                echo '<div class="alert alert-danger">Gagal membuka file CSV.</div>';
            }
        } else {
            echo '<div class="alert alert-warning">Silakan unggah file CSV terlebih dahulu.</div>';
        }
        ?>

    </div> <!-- hasilContainer -->

    <script>
        // Matrix Effect
        const canvas = document.getElementById("matrixCanvas");
        const ctx = canvas.getContext("2d");

        canvas.height = window.innerHeight;
        canvas.width = window.innerWidth;

        const letters = "01ABCDEFGHIJKLMNOPQRSTUVWXYZアカサタナハマヤラワ".split("");
        const fontSize = 14;
        const columns = canvas.width / fontSize;
        const drops = Array(Math.floor(columns)).fill(1);

        function drawMatrix() {
            ctx.fillStyle = "rgba(0, 0, 0, 0.05)";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#0F0";
            ctx.font = fontSize + "px monospace";

            for (let i = 0; i < drops.length; i++) {
                const text = letters[Math.floor(Math.random() * letters.length)];
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }

        setInterval(drawMatrix, 33); // sekitar 30 fps

        // Resize handler
        window.addEventListener("resize", () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
        // Simulasikan efek terminal + delay sebelum tampil hasil
        window.addEventListener("DOMContentLoaded", function() {
            const paragraphs = document.querySelectorAll("#scanOutput p");
            paragraphs.forEach((p, i) => {
                p.style.animationDelay = `${i * 0.8}s`;
            });

            setTimeout(function() {
                document.getElementById("loadingScreen").classList.add("hidden");
                document.getElementById("hasilContainer").classList.remove("hidden");
            }, 5000); // waktu total animasi
        });
    </script>

</body>

</html>