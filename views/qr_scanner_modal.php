<?php
// Query catalog for instant client-side lookup
$catalog_query = mysqli_query($conn, "SELECT id, judul, penulis, stok, gambar FROM buku ORDER BY id DESC");
$catalog_map = [];
$catalog_list = [];
if ($catalog_query) {
    while($row = mysqli_fetch_assoc($catalog_query)) {
        $catalog_map[$row['id']] = [
            'id' => (int)$row['id'],
            'judul' => $row['judul'],
            'penulis' => $row['penulis'],
            'stok' => (int)$row['stok'],
            'gambar' => $row['gambar'] ?: 'default.jpg'
        ];
        $catalog_list[] = $row;
    }
}
?>

<!-- MODAL SCANNER QR CODE -->
<div id="modalScanner" class="modal-overlay">
    <div class="modal-content" style="max-width: 480px; padding: 20px 24px;">
        <!-- MODAL HEADER -->
        <div class="modal-header" style="margin-bottom: 12px; padding-bottom: 10px; border-bottom: 1px solid var(--border-color);">
            <h3 style="display: flex; align-items: center; gap: 8px; font-size: 16px; margin: 0;">
                <i class="fa-solid fa-qrcode" style="color: var(--primary);"></i> Scan QR Code Buku
            </h3>
            <button class="modal-close" onclick="tutupModalScanner()">&times;</button>
        </div>

        <!-- MODE TAB SELECTOR -->
        <div style="display: flex; background: var(--bg-main); padding: 4px; border-radius: var(--radius-md); margin-bottom: 16px; border: 1px solid var(--border-color);">
            <button type="button" id="btnTabKamera" class="btn btn-sm" style="flex: 1; border-radius: 6px; background: var(--bg-card); color: var(--text-main); font-weight: 600; box-shadow: var(--shadow-sm);" onclick="switchScannerMode('kamera')">
                <i class="fa-solid fa-camera"></i> Kamera Webcam
            </button>
            <button type="button" id="btnTabSimulasi" class="btn btn-sm" style="flex: 1; border-radius: 6px; background: transparent; color: var(--text-muted); font-weight: 500;" onclick="switchScannerMode('simulasi')">
                <i class="fa-solid fa-vial"></i> Uji Simulasi
            </button>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            
            <!-- CAMERA BOX CONTAINER -->
            <div id="wrapperKameraSection" style="display: block;">
                <div id="scannerCameraWrapper" style="position: relative; width: 100%; height: 210px; background: #000; border-radius: 12px; overflow: hidden; border: 2px dashed var(--border-color); display: flex; align-items: center; justify-content: center;">
                    <div id="qr-reader" style="width: 100%;"></div>
                    <div id="scannerMessage" style="position: absolute; bottom: 10px; left: 50%; transform: translateX(-50%); background: rgba(15, 23, 42, 0.85); color: #fff; padding: 4px 14px; border-radius: 20px; font-size: 11px; white-space: nowrap; pointer-events: none; z-index: 10; border: 1px solid rgba(255,255,255,0.15);">
                        Arahkan kamera ke QR Code Buku...
                    </div>
                </div>
            </div>

            <!-- SIMULASI INPUT CONTAINER -->
            <div id="wrapperSimulasiSection" style="display: none; background: var(--bg-main); border: 1px solid var(--border-color); padding: 14px; border-radius: 12px;">
                <label class="form-label" style="font-size: 12px; margin-bottom: 6px; color: var(--text-muted);">
                    Pilih Buku dari Katalog (Untuk Menguji Tanpa Kamera):
                </label>
                <div style="display: flex; gap: 8px;">
                    <select id="simulasiBukuSelect" class="form-control" style="font-size: 13px; padding: 8px 12px; flex: 1;">
                        <option value="">-- Pilih Judul Buku --</option>
                        <?php foreach($catalog_list as $bl): ?>
                            <option value="<?= $bl['id'] ?>">ID #<?= $bl['id'] ?> - <?= htmlspecialchars($bl['judul']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" onclick="simulasiScan()" style="padding: 0 16px;">
                        <i class="fa-solid fa-bolt"></i> Scan
                    </button>
                </div>
            </div>

            <!-- RESULT CARD (HASIL SCAN) -->
            <div id="scannerResultCard" style="display: none; background: var(--bg-card); border: 1.5px solid var(--primary); border-radius: 12px; padding: 16px; box-shadow: var(--shadow-md); animation: slideIn 0.3s ease;">
                <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: var(--primary); letter-spacing: 0.05em; margin-bottom: 10px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-circle-check"></i> Buku Berhasil Ditemukan
                </div>
                
                <div style="display: flex; gap: 14px; align-items: flex-start;">
                    <img id="scannedBookCover" src="assets/img/default.jpg" style="width: 58px; height: 80px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border-color); flex-shrink: 0; box-shadow: var(--shadow-sm);">
                    <div style="flex: 1; min-width: 0;">
                        <span id="scannedBookBadge" class="badge badge-success" style="margin-bottom: 4px; font-size: 11px;">Tersedia</span>
                        <h4 id="scannedBookTitle" style="font-size: 14px; font-weight: 600; margin: 4px 0 2px 0; color: var(--text-main); line-height: 1.3; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">Judul Buku</h4>
                        <p id="scannedBookAuthor" style="font-size: 12px; color: var(--text-muted); margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"></p>
                    </div>
                </div>

                <!-- ADMIN QUICK RETURN SECTION -->
                <div id="adminReturnSection" style="display: none; margin-top: 14px; padding-top: 12px; border-top: 1px dashed var(--border-color);">
                    <div style="font-size: 12px; font-weight: 600; color: var(--text-main); margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-users-gear" style="color: var(--primary);"></i> Peminjam Aktif Saat Ini:
                    </div>
                    <div id="adminReturnList"></div>
                </div>
                
                <div style="margin-top: 14px; display: flex; gap: 10px; padding-top: 12px; border-top: 1px solid var(--border-color);">
                    <button type="button" class="btn btn-secondary btn-sm" style="flex: 1;" onclick="resetScannerResult()">
                        <i class="fa-solid fa-rotate-left"></i> Reset / Scan Lagi
                    </button>
                    <a id="scannedBookActionBtn" href="#" class="btn btn-primary btn-sm" style="flex: 1.2; text-align: center; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="fa-solid fa-hand-holding"></i> Pinjam Buku Ini
                    </a>
                </div>
            </div>

        </div>

        <!-- FOOTER BUTTON -->
        <div style="margin-top: 16px; text-align: right; border-top: 1px solid var(--border-color); padding-top: 12px;">
            <button type="button" class="btn btn-secondary btn-sm" onclick="tutupModalScanner()">
                Tutup
            </button>
        </div>
    </div>
</div>

<script src="assets/js/html5-qrcode.min.js"></script>
<script>
let html5QrCode = null;
let currentScannerMode = 'kamera';
let isScanDebounced = false;
const userRole = "<?= $_SESSION['role'] ?? 'user' ?>";
const localBukuMap = <?= json_encode($catalog_map) ?>;

function bukaModalScanner() {
    openModal('modalScanner');
    resetScannerResult();
    switchScannerMode('kamera');
}

function switchScannerMode(mode) {
    currentScannerMode = mode;
    const btnCam = document.getElementById('btnTabKamera');
    const btnSim = document.getElementById('btnTabSimulasi');
    const secCam = document.getElementById('wrapperKameraSection');
    const secSim = document.getElementById('wrapperSimulasiSection');

    if (mode === 'kamera') {
        btnCam.style.background = 'var(--bg-card)';
        btnCam.style.color = 'var(--text-main)';
        btnCam.style.fontWeight = '600';
        btnCam.style.boxShadow = 'var(--shadow-sm)';

        btnSim.style.background = 'transparent';
        btnSim.style.color = 'var(--text-muted)';
        btnSim.style.fontWeight = '500';
        btnSim.style.boxShadow = 'none';

        secCam.style.display = 'block';
        secSim.style.display = 'none';

        startCamera();
    } else {
        btnSim.style.background = 'var(--bg-card)';
        btnSim.style.color = 'var(--text-main)';
        btnSim.style.fontWeight = '600';
        btnSim.style.boxShadow = 'var(--shadow-sm)';

        btnCam.style.background = 'transparent';
        btnCam.style.color = 'var(--text-muted)';
        btnCam.style.fontWeight = '500';
        btnCam.style.boxShadow = 'none';

        secCam.style.display = 'none';
        secSim.style.display = 'block';

        stopCamera();
    }
}

function startCamera() {
    if (!html5QrCode) {
        html5QrCode = new Html5Qrcode("qr-reader");
    }

    // Dynamic large scanning box (85% coverage) so it detects QR codes easily from any angle/distance!
    const dynamicQrBox = function(viewfinderWidth, viewfinderHeight) {
        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
        const qrboxSize = Math.floor(minEdge * 0.85); // 85% area for effortless scanning
        return {
            width: qrboxSize,
            height: Math.floor(qrboxSize * 0.85)
        };
    };

    const config = { 
        fps: 10, 
        qrbox: dynamicQrBox,
        aspectRatio: 1.0,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        }
    };
    
    isScanDebounced = false;

    // 3-Tier Camera Fallback Chain (environment -> user -> default) for 100% device compatibility
    html5QrCode.start(
        { facingMode: "environment" }, 
        config, 
        onScanSuccess, 
        onScanFailure
    ).catch(err => {
        console.log("Environment camera failed, trying user camera...", err);
        return html5QrCode.start(
            { facingMode: "user" }, 
            config, 
            onScanSuccess, 
            onScanFailure
        );
    }).catch(err => {
        console.log("User camera failed, trying default camera...", err);
        return html5QrCode.start(
            true, 
            config, 
            onScanSuccess, 
            onScanFailure
        );
    }).catch(err => {
        console.error("Camera access error:", err);
        document.getElementById('scannerMessage').innerText = "Kamera tidak aktif / Gunakan Mode Uji Simulasi.";
    });
}

function stopCamera() {
    if (html5QrCode) {
        try {
            if (html5QrCode.isScanning) {
                html5QrCode.stop().then(() => {
                    console.log("Camera stopped.");
                }).catch(err => {
                    // Suppress scanner state warnings
                });
            }
        } catch (e) {
            // Suppress scanner state errors
        }
    }
}

function tutupModalScanner() {
    closeModal('modalScanner');
    stopCamera();
}

function onScanSuccess(decodedText, decodedResult) {
    if (isScanDebounced) return;

    try {
        let bookId = null;
        if (decodedText.startsWith('{')) {
            const data = JSON.parse(decodedText);
            bookId = data.id;
        } else if (decodedText.startsWith('BUKU-')) {
            bookId = decodedText.replace('BUKU-', '');
        } else {
            bookId = parseInt(decodedText);
        }

        if (bookId) {
            isScanDebounced = true;

            // Pause camera scanning to prevent spamming
            if (html5QrCode && html5QrCode.isScanning) {
                try {
                    html5QrCode.pause(true);
                } catch(e) {
                    console.log("Pause scanner error", e);
                }
            }

            prosesHasilScan(bookId);
        }
    } catch (e) {
        console.log("Error parsing payload", e);
    }
}

function onScanFailure(error) {
    // Left empty intentionally
}

function renderBookResult(b) {
    document.getElementById('scannedBookCover').src = 'assets/img/' + (b.gambar || 'default.jpg');
    document.getElementById('scannedBookTitle').innerText = b.judul;
    document.getElementById('scannedBookTitle').title = b.judul;
    document.getElementById('scannedBookAuthor').innerText = "Penulis: " + b.penulis;
    
    const badge = document.getElementById('scannedBookBadge');
    const actionBtn = document.getElementById('scannedBookActionBtn');
    const adminSec = document.getElementById('adminReturnSection');
    const adminList = document.getElementById('adminReturnList');

    if (userRole === 'admin') {
        badge.className = b.stok > 0 ? "badge badge-success" : "badge badge-danger";
        badge.innerText = b.stok > 0 ? "Tersedia (" + b.stok + ")" : "Stok Habis (0)";
        
        actionBtn.innerHTML = '<i class="fa-solid fa-pen-to-square"></i> Edit Data Buku';
        actionBtn.className = "btn btn-primary btn-sm";
        actionBtn.style.opacity = "1";
        actionBtn.style.pointerEvents = "auto";
        actionBtn.setAttribute('href', 'edit_buku.php?id=' + b.id);
        actionBtn.onclick = null;

        // Render Active Borrowers for Admin Quick Return
        adminSec.style.display = 'block';
        if (b.peminjam && b.peminjam.length > 0) {
            let html = '';
            b.peminjam.forEach(p => {
                html += `
                    <div style="background: var(--bg-main); border: 1px solid var(--border-color); padding: 10px 12px; border-radius: 8px; margin-bottom: 8px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong style="font-size: 13px; color: var(--text-main);">${p.nama}</strong>
                            <div style="font-size: 11.5px; color: var(--text-muted);">${p.email}</div>
                            <div style="font-size: 11px; color: var(--primary); margin-top: 2px;">Batas Kembali: ${p.tanggal_kembali}</div>
                        </div>
                        <a href="kembali.php?id=${p.peminjaman_id}" class="btn btn-success btn-sm" onclick="return bukaKonfirmasi(this.href, 'Konfirmasi pengembalian buku dari ${p.nama}?', 'Pengembalian Buku', 'success')">
                            <i class="fa-solid fa-rotate-left"></i> Kembalikan
                        </a>
                    </div>
                `;
            });
            adminList.innerHTML = html;
        } else {
            adminList.innerHTML = `
                <div style="font-size: 12px; color: var(--text-muted); background: var(--bg-main); padding: 8px 12px; border-radius: 6px; text-align: center;">
                    <i class="fa-solid fa-circle-info"></i> Buku ini sedang tidak dipinjam oleh siswa manapun.
                </div>
            `;
        }
    } else {
        adminSec.style.display = 'none';
        
        if (b.stok > 0) {
            badge.className = "badge badge-success";
            badge.innerText = "Tersedia (" + b.stok + ")";
            
            actionBtn.innerHTML = '<i class="fa-solid fa-hand-holding"></i> Pinjam Buku Ini';
            actionBtn.className = "btn btn-primary btn-sm";
            actionBtn.style.opacity = "1";
            actionBtn.style.pointerEvents = "auto";
            
            actionBtn.setAttribute('href', 'pinjam.php?id=' + b.id);
            actionBtn.onclick = function() {
                tutupModalScanner();
                return true;
            };
        } else {
            badge.className = "badge badge-danger";
            badge.innerText = "Stok Habis (0)";
            
            actionBtn.innerHTML = '<i class="fa-solid fa-ban"></i> Stok Habis';
            actionBtn.className = "btn btn-secondary btn-sm";
            actionBtn.style.opacity = "0.6";
            actionBtn.style.pointerEvents = "none";
            actionBtn.removeAttribute('href');
            actionBtn.onclick = (e) => e.preventDefault();
        }
    }

    document.getElementById('scannerResultCard').style.display = 'block';
    showToast("<i class='fa-solid fa-circle-check'></i> QR Code berhasil dipindai!", "success");
}

function prosesHasilScan(bookId) {
    const numericId = parseInt(bookId);

    // Fetch via API to get real-time active borrowers list for Admin
    fetch('api_get_buku.php?id=' + numericId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                renderBookResult(data.buku);
            } else {
                showToast("<i class='fa-solid fa-circle-xmark'></i> " + data.message, "danger");
                isScanDebounced = false;
            }
        })
        .catch(err => {
            console.error("Scan fetch error:", err);
            if (localBukuMap && localBukuMap[numericId]) {
                renderBookResult(localBukuMap[numericId]);
            } else {
                showToast("<i class='fa-solid fa-circle-xmark'></i> Gagal mengambil data buku", "danger");
                isScanDebounced = false;
            }
        });
}

function resetScannerResult() {
    document.getElementById('scannerResultCard').style.display = 'none';
    isScanDebounced = false;

    // Resume camera scanning smoothly
    if (html5QrCode) {
        try {
            if (html5QrCode.getState && html5QrCode.getState() === 3) { // 3 = PAUSED
                html5QrCode.resume();
            }
        } catch(e) {
            console.log("Resume camera error", e);
        }
    }
}

function simulasiScan() {
    const bookId = document.getElementById('simulasiBukuSelect').value;
    if (!bookId) {
        showToast("<i class='fa-solid fa-triangle-exclamation'></i> Pilih buku untuk simulasi terlebih dahulu", "warning");
        return;
    }
    prosesHasilScan(bookId);
}
</script>
