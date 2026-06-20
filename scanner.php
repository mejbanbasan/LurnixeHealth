<?php
/**
 * Lurnixe Health Card System - Live QR Scanner Registry
 * June 2026
 */
$page_title = "QR Scanner Portal";
require_once __DIR__ . '/includes/header.php';
?>

<style>
<style>
/* Custom style overrides for scanner page to make it feel like a premium mobile scanning app */
body {
    background-color: #121212 !important; /* dark app background */
    color: #ffffff !important;
}
.mobile-header {
    background-color: #1a1a1a !important;
    border-bottom: 1px solid #2d2d2d !important;
}
.mobile-header .text-dark {
    color: #ffffff !important;
}
.scanner-container {
    max-width: 480px;
    margin: 0 auto;
    padding: 24px 16px 80px 16px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 136px);
}
.scanner-title-section {
    text-align: center;
    margin-bottom: 24px;
}
.scanner-title-section h2 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 8px;
}
.scanner-title-section p {
    font-size: 0.85rem;
    color: #aaaaaa;
    max-width: 280px;
    margin: 0 auto;
}
.scanner-viewport-wrapper {
    position: relative;
    width: 100%;
    max-width: 290px !important;
    aspect-ratio: 1;
    border-radius: 24px;
    overflow: hidden;
    background: #000000;
    border: 3px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.6);
    margin-bottom: 24px;
}
#reader {
    width: 100%;
    height: 100%;
    border: none !important;
}
#reader video {
    object-fit: cover !important;
    width: 100% !important;
    height: 100% !important;
}
.scanner-frame-overlay {
    position: absolute;
    top: 12%;
    left: 12%;
    right: 12%;
    bottom: 12%;
    border: 2px solid rgba(39, 174, 96, 0.3);
    border-radius: 16px;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.65);
    z-index: 5;
    pointer-events: none;
}
.scanner-frame-overlay::before,
.scanner-frame-overlay::after,
.scanner-frame-overlay span::before,
.scanner-frame-overlay span::after {
    content: "";
    position: absolute;
    width: 24px;
    height: 24px;
    border-color: #27ae60 !important; /* glowing neon green corners */
    border-style: solid;
    z-index: 6;
}
/* Top-Left Corner */
.scanner-frame-overlay::before {
    top: -3px;
    left: -3px;
    border-width: 4px 0 0 4px;
    border-top-left-radius: 8px;
}
/* Top-Right Corner */
.scanner-frame-overlay::after {
    top: -3px;
    right: -3px;
    border-width: 4px 4px 0 0;
    border-top-right-radius: 8px;
}
/* Bottom-Left Corner */
.scanner-frame-overlay span::before {
    bottom: -3px;
    left: -3px;
    border-width: 0 0 4px 4px;
    border-bottom-left-radius: 8px;
}
/* Bottom-Right Corner */
.scanner-frame-overlay span::after {
    bottom: -3px;
    right: -3px;
    border-width: 0 4px 4px 0;
    border-bottom-right-radius: 8px;
}
.scanner-laser {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, transparent, #27ae60, transparent);
    box-shadow: 0 0 12px #27ae60, 0 0 4px #27ae60;
    animation: scanning 2.5s linear infinite;
    z-index: 10;
    pointer-events: none;
}
@keyframes scanning {
    0% { top: 12%; }
    50% { top: 88%; }
    100% { top: 12%; }
}
.scanner-status-banner {
    width: 100%;
    max-width: 290px;
    padding: 12px 16px;
    border-radius: 12px;
    background-color: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 0.85rem;
    color: #e0e0e0;
    text-align: center;
    margin-bottom: 24px;
}
.scanner-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 32px;
}
.scanner-fab-btn {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    background-color: #27ae60;
    color: #ffffff;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    box-shadow: 0 4px 15px rgba(39, 174, 96, 0.4);
    transition: all 0.2s ease;
    cursor: pointer;
}
.scanner-fab-btn:active {
    transform: scale(0.92);
}
.scanner-fab-btn.btn-stop {
    background-color: #c0392b;
    box-shadow: 0 4px 15px rgba(192, 57, 43, 0.4);
}
.camera-select-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: rgba(255, 255, 255, 0.1);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}
.camera-select-btn:active {
    background-color: rgba(255, 255, 255, 0.2);
}
.manual-verification-box {
    width: 100%;
    max-width: 340px;
    padding: 20px;
    border-radius: 16px;
    background-color: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.manual-verification-box h6 {
    font-size: 0.75rem;
    font-weight: 700;
    color: #aaaaaa;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}
.manual-input-group {
    display: flex;
    border: 1px solid rgba(255, 255, 255, 0.15);
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 10px;
    overflow: hidden;
    padding: 2px;
}
.manual-input-group input {
    flex: 1;
    background: transparent;
    border: none;
    padding: 10px 14px;
    color: #ffffff;
    font-family: monospace;
    font-size: 0.95rem;
    font-weight: 700;
    outline: none;
    text-transform: uppercase;
}
.manual-input-group button {
    background-color: #27ae60;
    border: none;
    color: #ffffff;
    padding: 0 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}
.manual-input-group button:active {
    background-color: #219653;
}
</style>

<div class="scanner-container">
    <div class="scanner-title-section">
        <h2>Scan Member Card</h2>
        <p>Position the QR code inside the frame to verify membership credentials</p>
    </div>

    <!-- Centered Scanner Viewport -->
    <div class="scanner-viewport-wrapper" id="scannerWrapper">
        <div id="reader"></div>
        <div class="scanner-laser" id="laser"></div>
        <div class="scanner-frame-overlay" id="overlay"><span></span></div>
    </div>

    <!-- Refined Status Banner -->
    <div id="scannerStatus" class="scanner-status-banner">
        <i class="fa-solid fa-spinner fa-spin me-2 text-success"></i>Initializing camera...
    </div>

    <!-- Hidden Camera Selector -->
    <select id="cameraSelect" class="form-select form-select-sm mx-auto mb-3" style="max-width: 250px; display: none; background-color: #2d2d2d; color: white; border: 1px solid #444;">
        <option value="">Switch Camera</option>
    </select>

    <!-- Camera Controls -->
    <div class="scanner-controls">
        <button id="cameraSwitchBtn" class="camera-select-btn" title="Switch Camera" style="display: none;">
            <i class="fa-solid fa-camera-rotate"></i>
        </button>
        <button id="startScanBtn" class="scanner-fab-btn" title="Start Camera" style="display: none;">
            <i class="fa-solid fa-play"></i>
        </button>
        <button id="stopScanBtn" class="scanner-fab-btn btn-stop" title="Stop Camera" style="display: none;">
            <i class="fa-solid fa-square"></i>
        </button>
    </div>

    <!-- Fallback Manual Verification Box -->
    <div class="manual-verification-box">
        <h6><i class="fa-solid fa-keyboard text-success"></i> Verify Manually</h6>
        <form id="manualVerifyForm">
            <div class="manual-input-group">
                <input type="text" id="manualMemberId" placeholder="e.g. LFC000124" required autocomplete="off" style="letter-spacing: 0.5px;">
                <button type="submit">Verify</button>
            </div>
            <div id="manualError" class="text-danger small mt-2 fw-semibold" style="display: none; font-size: 0.75rem;"></div>
        </form>
    </div>
</div>

<!-- Load html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const readerDiv = document.getElementById("reader");
    const statusDiv = document.getElementById("scannerStatus");
    const cameraSelect = document.getElementById("cameraSelect");
    const switchBtn = document.getElementById("cameraSwitchBtn");
    const startBtn = document.getElementById("startScanBtn");
    const stopBtn = document.getElementById("stopScanBtn");
    const laser = document.getElementById("laser");
    const overlay = document.getElementById("overlay");
    
    let html5QrScanner = null;
    let cameraList = [];
    let activeCameraId = null;

    // Check if CDN loaded
    if (typeof Html5Qrcode === "undefined") {
        statusDiv.innerHTML = '<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Failed to load scanner library. Reload page.</span>';
        laser.style.display = 'none';
        overlay.style.display = 'none';
        return;
    }

    // Initialize HTML5 QR Code instance
    html5QrScanner = new Html5Qrcode("reader");

    // Fetch cameras
    Html5Qrcode.getCameras().then(devices => {
        cameraList = devices;
        if (devices && devices.length > 0) {
            statusDiv.innerHTML = '<i class="fa-solid fa-circle-check me-2 text-success"></i>Camera found. Ready to scan.';
            
            // Populate select dropdown
            cameraSelect.innerHTML = "";
            devices.forEach((device, index) => {
                const opt = document.createElement("option");
                opt.value = device.id;
                opt.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(opt);
            });

            // Show controls
            startBtn.style.display = "flex";
            if (devices.length > 1) {
                switchBtn.style.display = "flex";
            }

            // Try to auto-start with back camera
            let backCam = devices.find(device => device.label.toLowerCase().includes("back") || device.label.toLowerCase().includes("environment") || device.label.toLowerCase().includes("rear"));
            let selectedCamId = backCam ? backCam.id : devices[0].id;
            
            cameraSelect.value = selectedCamId;
            startScanning(selectedCamId);
        } else {
            statusDiv.innerHTML = '<span class="text-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>No camera device detected.</span>';
            laser.style.display = 'none';
            overlay.style.display = 'none';
        }
    }).catch(err => {
        console.error("Camera access failed", err);
        statusDiv.innerHTML = '<span class="text-danger"><i class="fa-solid fa-ban me-2"></i>Camera permission denied or blocked.</span>';
        laser.style.display = 'none';
        overlay.style.display = 'none';
    });

    // Start scan function
    function startScanning(cameraId) {
        statusDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2 text-success"></i>Accessing camera stream...';
        laser.style.display = 'block';
        overlay.style.display = 'block';

        html5QrScanner.start(
            cameraId,
            {
                fps: 20,
                qrbox: (width, height) => {
                    // Fit the scan area nicely within overlay frame
                    return { width: width * 0.75, height: height * 0.75 };
                },
                aspectRatio: 1.0
            },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            activeCameraId = cameraId;
            statusDiv.innerHTML = '<i class="fa-solid fa-video me-2 text-success"></i>Camera active. Scanning...';
            startBtn.style.display = "none";
            stopBtn.style.display = "flex";
        }).catch(err => {
            console.error("Failed to start scanner", err);
            statusDiv.innerHTML = `<span class="text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Failed to start: ${err}</span>`;
            laser.style.display = 'none';
            overlay.style.display = 'none';
            startBtn.style.display = "flex";
            stopBtn.style.display = "none";
        });
    }

    // Stop scan function
    function stopScanning() {
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                statusDiv.innerHTML = '<i class="fa-solid fa-circle-stop me-2 text-warning"></i>Camera stopped.';
                startBtn.style.display = "flex";
                stopBtn.style.display = "none";
                laser.style.display = 'none';
                overlay.style.display = 'none';
            }).catch(err => {
                console.error("Failed to stop scanner", err);
            });
        }
    }

    // On Success
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan matched text: ${decodedText}`);
        
        // Stop scanning immediately
        stopScanning();
        
        statusDiv.innerHTML = '<span class="text-success fw-bold"><i class="fa-solid fa-circle-check me-2"></i>QR Code detected! Redirecting...</span>';

        // Check if QR matches member profile URL pattern
        let redirectUrl = decodedText;
        if (decodedText.includes("member.php?id=")) {
            window.location.href = redirectUrl;
        } else {
            // Treat as raw ID fallback
            window.location.href = `member.php?id=${encodeURIComponent(decodedText)}`;
        }
    }

    // On Failure (silent)
    function onScanFailure(error) {
        // quiet
    }

    // Button event handlers
    startBtn.addEventListener("click", () => {
        const selectedId = cameraSelect.value;
        if (selectedId) startScanning(selectedId);
    });

    stopBtn.addEventListener("click", () => {
        stopScanning();
    });

    // Switch camera button handler
    switchBtn.addEventListener("click", () => {
        if (cameraList.length <= 1) return;
        let currentIndex = cameraList.findIndex(device => device.id === cameraSelect.value);
        let nextIndex = (currentIndex + 1) % cameraList.length;
        let nextCameraId = cameraList[nextIndex].id;
        cameraSelect.value = nextCameraId;
        
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                startScanning(nextCameraId);
            });
        } else {
            startScanning(nextCameraId);
        }
    });

    cameraSelect.addEventListener("change", () => {
        const selectedId = cameraSelect.value;
        if (selectedId) {
            if (html5QrScanner && html5QrScanner.isScanning) {
                html5QrScanner.stop().then(() => {
                    startScanning(selectedId);
                });
            } else {
                startScanning(selectedId);
            }
        }
    });

    // Handle Manual Verification form submission
    document.getElementById("manualVerifyForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const errDiv = document.getElementById("manualError");
        errDiv.style.display = "none";
        
        const memberId = document.getElementById("manualMemberId").value.trim().toUpperCase();
        if (memberId) {
            window.location.href = `member.php?id=${encodeURIComponent(memberId)}`;
        } else {
            errDiv.innerHTML = "Please enter a valid Member ID.";
            errDiv.style.display = "block";
        }
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
