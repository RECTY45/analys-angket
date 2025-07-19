<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Upload Penilaian Responden</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes typing {
            from {
                width: 0
            }

            to {
                width: 100%
            }
        }

        @keyframes blink {
            50% {
                border-color: transparent
            }
        }

        .typewriter {
            overflow: hidden;
            border-right: 2px solid #00ff00;
            white-space: nowrap;
            animation: typing 4s steps(40, end), blink .8s step-end infinite;
            font-family: 'Courier New', monospace;
        }

        #hackerOverlay {
            display: none;
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

        #credit {
            position: absolute;
            bottom: 10px;
            right: 20px;
            font-family: monospace;
            color: #00ff00;
            font-size: 12px;
            opacity: 0.8;
        }
    </style>
</head>

<body class="bg-gray-900 flex items-center justify-center min-h-screen">
    <canvas id="matrixCanvas"></canvas>

    <!-- Loading Hacker Overlay -->
    <div id="hackerOverlay" class="fixed inset-0 bg-black text-green-400 z-50 flex flex-col p-10 font-mono">
        <div class="text-lg md:text-xl font-bold mb-4 animate-pulse text-green-500">[ ANALYZING DATA STREAM... ]</div>
        <div id="terminal" class="flex-1 overflow-y-auto whitespace-pre text-sm leading-relaxed"></div>
        <div id="credit">Built by FERY DEV | 2025 ©</div>
    </div>

    <!-- Upload Card -->
    <div class="bg-white shadow-2xl rounded-xl p-8 w-full max-w-md relative">
        <h2 class="text-2xl font-bold text-center text-green-700 mb-2">Upload Penilaian Responden</h2>
        <p class="text-center text-xs text-gray-500 mb-6 italic">Sistem analisis data responden google form<span class="text-green-600 font-semibold"> FERY DEV</span></p>
        <form id="uploadForm" action="ouput.php" method="POST" enctype="multipart/form-data" class="space-y-4">
            <input type="file" name="file_csv" accept=".csv" required
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100 cursor-pointer" />
            <button type="submit"
                class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 font-semibold transition duration-200">
                Cek Hasil
            </button>
        </form>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const hackerOverlay = document.getElementById('hackerOverlay');
        const terminal = document.getElementById('terminal');

        const fakeLogs = [
            "[INFO] Mendeteksi struktur data...",
            "[INFO] Parsing file .csv...",
            "[SCAN] Menghitung validitas responden...",
            "[TRACE] Melacak ID unik...",
            "[TRACK] Sinkronisasi ke model analisis...",
            "[AI] Model aktif... memproses...",
            "[RESULT] Hasil akan ditampilkan...",
            "[COMPLETE] Data siap dianalisis."
        ];

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

        setInterval(drawMatrix, 33);

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            hackerOverlay.style.display = 'flex';
            terminal.innerHTML = "";

            let i = 0;
            const interval = setInterval(() => {
                if (i < fakeLogs.length) {
                    const line = document.createElement("div");
                    line.classList.add("typewriter");
                    line.textContent = fakeLogs[i];
                    terminal.appendChild(line);
                    i++;
                } else {
                    clearInterval(interval);
                    setTimeout(() => {
                        form.submit();
                    }, 1000);
                }
            }, 900);
        });
    </script>
</body>

</html>
