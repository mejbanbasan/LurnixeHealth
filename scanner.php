<?php
/**
 * Lurnixe Health Card System - Live QR Scanner Registry
 * June 2026
 */
$page_title = "QR Scanner Portal";
$body_class = "scanner-page";
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* Custom style overrides for scanner page to make it feel like a premium mobile scanning app */
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
.scanner-viewport-wrapper {
    position: relative;
    width: 100%;
    max-width: 290px !important;
    aspect-ratio: 1;
    border-radius: 24px;
    overflow: hidden;
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
.scanner-controls {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}
.camera-select-btn {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background-color: rgba(0, 0, 0, 0.05);
    color: var(--primary-blue);
    border: 1px solid rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all 0.2s ease;
    cursor: pointer;
}
.camera-select-btn:active {
    background-color: rgba(0, 0, 0, 0.1);
}
@media (max-width: 991.98px) {
    .camera-select-btn {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
    }
    .camera-select-btn:active {
        background-color: rgba(255, 255, 255, 0.2) !important;
    }
}

/* File Upload Section */
.scanner-upload-section {
    width: 100%;
    max-width: 290px;
    margin-top: 10px;
    display: flex;
    justify-content: center;
}
.scanner-upload-btn {
    width: 100%;
    padding: 12px 24px;
    background: rgba(39, 174, 96, 0.08);
    color: #27ae60;
    border: 1.5px dashed #27ae60;
    border-radius: 16px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s ease;
}
.scanner-upload-btn:hover {
    background: rgba(39, 174, 96, 0.12);
}
.scanner-upload-btn:active {
    transform: scale(0.98);
}
@media (max-width: 991.98px) {
    .scanner-upload-btn {
        background: rgba(39, 174, 96, 0.15);
        color: #2ecc71;
        border-color: #2ecc71;
    }
}

/* Center Branded Logo Overlay */
.scanner-center-logo {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 8;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.scanner-center-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    opacity: 0.85;
}
</style>

<div class="scanner-container">
    <!-- Centered Scanner Viewport -->
    <div class="scanner-viewport-wrapper" id="scannerWrapper">
        <div id="reader"></div>
        <div class="scanner-laser" id="laser"></div>
        <div class="scanner-frame-overlay" id="overlay"><span></span></div>
        <!-- Branded Logo Overlay inside QR scan area -->
        <div class="scanner-center-logo">
            <img src="<?php echo BASE_URL; ?>assets/images/qr_logo.png" alt="Scanner Logo">
        </div>
    </div>

    <!-- Error message alert -->
    <div id="scannerError" class="alert alert-danger text-center mx-auto" style="display: none; max-width: 290px; font-size: 0.85rem; border-radius: 12px; border: 1px solid rgba(231, 76, 60, 0.2); background-color: rgba(231, 76, 60, 0.05); color: #e74c3c; margin-bottom: 20px;"></div>

    <!-- Camera Controls (minimal switch camera btn) -->
    <div class="scanner-controls">
        <button id="cameraSwitchBtn" class="camera-select-btn" title="Switch Camera" style="display: none;">
            <i class="fa-solid fa-camera-rotate"></i>
        </button>
    </div>

    <!-- File/Image Upload Option -->
    <div class="scanner-upload-section">
        <button id="uploadQrBtn" class="scanner-upload-btn">
            <i class="fa-solid fa-file-image"></i>
            <span>Upload QR Image</span>
        </button>
        <input type="file" id="qrFileInput" accept="image/*" style="display: none;">
    </div>
</div>

<!-- Load html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const readerDiv = document.getElementById("reader");
    const switchBtn = document.getElementById("cameraSwitchBtn");
    const laser = document.getElementById("laser");
    const overlay = document.getElementById("overlay");
    const errorDiv = document.getElementById("scannerError");
    const uploadBtn = document.getElementById("uploadQrBtn");
    const fileInput = document.getElementById("qrFileInput");

    let html5QrScanner = null;
    let cameraList = [];
    let currentFacingMode = "environment";

    // Check if CDN loaded
    if (typeof Html5Qrcode === "undefined") {
        showError("Failed to load scanner library. Please check your internet connection.");
        laser.style.display = 'none';
        overlay.style.display = 'none';
        return;
    }

    // Initialize HTML5 QR Code instance
    html5QrScanner = new Html5Qrcode("reader");

    // Start Live camera scanning immediately on load
    startScanning({ facingMode: currentFacingMode });

    // Start scan function
    function startScanning(cameraConstraint) {
        laser.style.display = 'block';
        overlay.style.display = 'block';

        html5QrScanner.start(
            cameraConstraint,
            {
                fps: 20,
                qrbox: (width, height) => {
                    return { width: width * 0.75, height: height * 0.75 };
                },
                aspectRatio: 1.0
            },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            // Once started successfully, query device list in background to show switch button
            Html5Qrcode.getCameras().then(devices => {
                cameraList = devices;
                if (devices && devices.length > 1) {
                    switchBtn.style.display = "flex";
                } else {
                    switchBtn.style.display = "none";
                }
            }).catch(err => {
                console.warn("Error enumerating cameras in background:", err);
            });
        }).catch(err => {
            console.error("Failed to start scanner:", err);
            // Fallback: If rear camera failed, try front camera
            if (cameraConstraint.facingMode === "environment") {
                console.log("Rear camera failed, attempting front camera fallback...");
                currentFacingMode = "user";
                startScanning({ facingMode: currentFacingMode });
            } else {
                showError("Camera access denied or blocked. Please upload a QR image below or check browser settings.");
                laser.style.display = 'none';
                overlay.style.display = 'none';
            }
        });
    }

    // On Scan Success
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan matched text: ${decodedText}`);
        
        // Stop scanning immediately to prevent duplicate runs
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                handleSuccess(decodedText);
            }).catch(err => {
                console.error("Failed to stop scanner on success:", err);
                handleSuccess(decodedText);
            });
        } else {
            handleSuccess(decodedText);
        }
    }

    // On Scan Failure (quiet/silent)
    function onScanFailure(error) {
        // quiet
    }

    // Success Redirection Handler
    function handleSuccess(decodedText) {
        if (decodedText.includes("member.php?id=")) {
            window.location.href = decodedText;
        } else {
            window.location.href = `member.php?id=${encodeURIComponent(decodedText)}`;
        }
    }

    // Show error helper
    function showError(message) {
        errorDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-2"></i>${message}`;
        errorDiv.style.display = "block";
    }

    // Hide error helper
    function hideError() {
        errorDiv.innerHTML = "";
        errorDiv.style.display = "none";
    }

    // Switch camera button handler
    switchBtn.addEventListener("click", () => {
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                startScanning({ facingMode: currentFacingMode });
            }).catch(err => {
                console.error("Failed to stop scanner before switching:", err);
            });
        }
    });

    // File Upload event handlers
    uploadBtn.addEventListener("click", () => {
        fileInput.click();
    });

    fileInput.addEventListener("change", e => {
        if (e.target.files.length === 0) return;
        const file = e.target.files[0];

        // Step 1: Stop live camera scanning if running
        let stopPromise = Promise.resolve();
        if (html5QrScanner && html5QrScanner.isScanning) {
            stopPromise = html5QrScanner.stop();
        }

        stopPromise.then(() => {
            hideError();
            
            // Set laser and overlay to display none during static file scanning
            laser.style.display = 'none';
            overlay.style.display = 'none';

            // Step 2: Scan the image file
            html5QrScanner.scanFile(file, false)
                .then(decodedText => {
                    handleSuccess(decodedText);
                })
                .catch(err => {
                    console.error("File scanning failed:", err);
                    showError("No valid QR code found in this image. Please try a clearer image or use the live scanner.");
                    fileInput.value = ""; // Reset file input
                    
                    // Restart live scanning
                    startScanning({ facingMode: currentFacingMode });
                });
        }).catch(err => {
            console.error("Failed to stop camera scanner for file upload:", err);
        });
    });
});
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>