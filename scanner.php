<?php
/**
 * Lurnixe Health Card System - Live QR Scanner Registry
 * June 2026 - Rebuilt from Scratch
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
    background-color: #000000 !important; /* Force black background to prevent white boxes */
    background: #000000 !important;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
}
#reader {
    position: relative !important;
    width: 100% !important;
    height: 100% !important;
    border: none !important;
    background-color: transparent !important;
    background: transparent !important;
    z-index: 1 !important;
}
#reader video {
    position: relative !important;
    z-index: 2 !important;
    object-fit: cover !important;
    width: 100% !important;
    height: 100% !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    background-color: transparent !important;
    background: transparent !important;
    
    /* Hardware acceleration trigger without overriding library scaleX transforms */
    will-change: transform, opacity !important;
    backface-visibility: hidden !important;
    -webkit-backface-visibility: hidden !important;
}
/* Force all direct and nested child elements inside #reader to have transparent backgrounds and no borders/shadows */
#reader, #reader * {
    background: transparent !important;
    background-color: transparent !important;
    border: none !important;
    box-shadow: none !important;
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

/* Tap to Activate Overlay Prompt */
.tap-to-scan-prompt {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: rgba(0, 0, 0, 0.85) !important;
    background: rgba(0, 0, 0, 0.85) !important;
    color: #ffffff;
    display: none;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    font-family: var(--font-heading);
    font-size: 0.88rem;
    font-weight: 600;
    z-index: 15; /* sits above reader, laser, and overlays */
    cursor: pointer;
    text-align: center;
    padding: 20px;
    border-radius: 20px;
}
.tap-to-scan-prompt i {
    font-size: 2.2rem;
    color: #27ae60;
    margin-bottom: 12px;
    animation: pulseIcon 2s infinite;
}
@keyframes pulseIcon {
    0% { transform: scale(1); opacity: 0.8; }
    50% { transform: scale(1.1); opacity: 1; }
    100% { transform: scale(1); opacity: 0.8; }
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
        <!-- Self-Healing Tap to Activate prompt (for iOS/Safari gesture restrictions) -->
        <div id="tapToScanPrompt" class="tap-to-scan-prompt">
            <i class="fa-solid fa-camera"></i>
            <span>Tap to start camera</span>
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

<!-- Load stable html5-qrcode CDN from cdnjs -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const readerDiv = document.getElementById("reader");
    const switchBtn = document.getElementById("cameraSwitchBtn");
    const laser = document.getElementById("laser");
    const overlay = document.getElementById("overlay");
    const errorDiv = document.getElementById("scannerError");
    const uploadBtn = document.getElementById("uploadQrBtn");
    const fileInput = document.getElementById("qrFileInput");
    const tapPrompt = document.getElementById("tapToScanPrompt");
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

    // Stop all active MediaStream tracks on the page to prevent camera locks
    function stopAllActiveMediaStreams() {
        console.log("Releasing active browser camera tracks...");
        try {
            const videos = document.querySelectorAll("video");
            videos.forEach(video => {
                if (video.srcObject instanceof MediaStream) {
                    video.srcObject.getTracks().forEach(track => {
                        console.log(`Stopping active track: "${track.label}"`);
                        track.stop();
                    });
                    video.srcObject = null;
                }
            });
        } catch (e) {
            console.warn("Manual MediaStream cleanup error:", e);
        }
    }

    // Start Live camera scanning immediately on load
    startScanning(currentFacingMode, true);

    // Start scan function
    function startScanning(facingMode, useResolution = false) {
        console.log(`Starting scan: facingMode=${facingMode}, useResolution=${useResolution}`);
        laser.style.display = 'block';
        overlay.style.display = 'block';
        hideTapToScanPrompt();

        const config = {
            fps: 20,
            qrbox: (width, height) => {
                return { width: width * 0.75, height: height * 0.75 };
            },
            // Disable experimental BarcodeDetector API which freezes on some device GPUs
            useBarCodeDetectorIfSupported: false,
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: false
            }
        };

        if (useResolution) {
            config.videoConstraints = {
                facingMode: facingMode,
                width: { ideal: 1280 },
                height: { ideal: 720 }
            };
        }

        html5QrScanner.start(
            { facingMode: facingMode }, // EXACTLY 1 key!
            config,
            onScanSuccess,
            onScanFailure
        ).then(() => {
            // WebKit rendering layer repaint force (keeps back camera active on mobile WebKit compositors)
            setTimeout(() => {
                try {
                    const video = readerDiv.querySelector("video");
                    if (video) {
                        const originalDisplay = video.style.display || "block";
                        video.style.display = "none";
                        video.offsetHeight; // force layout reflow
                        video.style.display = originalDisplay;
                        
                        video.style.opacity = "0.99";
                        setTimeout(() => {
                            video.style.opacity = "1";
                        }, 50);

                        if (video.paused) {
                            video.play().catch(() => {});
                        }
                    }
                } catch (e) {}
            }, 400);
            
            // Query device list in background to show switch camera button
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
            console.error(`Camera start rejected/failed: ${err}`);
            
            // Self-Healing Fallback sequence:
            if (useResolution) {
                console.warn("Retrying start scanning with relaxed constraints (no resolution values)...");
                startScanning(facingMode, false);
            } else if (facingMode === "environment") {
                console.warn("Rear camera constraint failed. Attempting front camera fallback...");
                currentFacingMode = "user";
                startScanning(currentFacingMode, true);
            } else {
                console.warn("Camera streams blocked by browser. Activating manual tap-to-scan prompt.");
                showTapToScanPrompt("Tap here to start camera");
                laser.style.display = 'none';
                overlay.style.display = 'none';
            }
        });
    }

    // Autoplay watchdog: forces video playing state if browser policy suspends it
    setInterval(() => {
        try {
            const video = readerDiv.querySelector("video");
            if (video && video.paused && !video.ended) {
                console.warn("Video stream paused by user-agent/autoplay policy. Force resuming play state...");
                video.play().then(() => {
                    console.log("Stream successfully resumed.");
                }).catch(e => {
                    console.error(`Failed to force play video: ${e}`);
                });
            }
        } catch (e) {}
    }, 1000);

    // On Scan Success
    function onScanSuccess(decodedText, decodedResult) {
        console.log(`Scan matched text: ${decodedText}`);
        
        // Stop scanning immediately to prevent duplicate runs
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                stopAllActiveMediaStreams();
                handleSuccess(decodedText);
            }).catch(err => {
                console.error("Failed to stop scanner on success:", err);
                stopAllActiveMediaStreams();
                handleSuccess(decodedText);
            });
        } else {
            stopAllActiveMediaStreams();
            handleSuccess(decodedText);
        }
    }

    // On Scan Failure (quiet/silent)
    function onScanFailure(error) {
        // quiet
    }

    // Success Redirection Handler
    function handleSuccess(decodedText) {
        console.log(`Redirecting to details page for: ${decodedText}`);
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
            console.log("Stopping scanner for camera switch...");
            html5QrScanner.stop().then(() => {
                stopAllActiveMediaStreams();
                console.log("Scanner stopped. Waiting 300ms to allow hardware release...");
                setTimeout(() => {
                    currentFacingMode = (currentFacingMode === "environment") ? "user" : "environment";
                    startScanning(currentFacingMode, true);
                }, 300);
            }).catch(err => {
                console.error("Failed to stop scanner before switching:", err);
            });
        }
    });

    // Tap to Scan Click/Tap listener (solves Safari page-load user gesture restrictions)
    tapPrompt.addEventListener("click", () => {
        console.log("User tapped prompt overlay. Re-triggering camera request under active gesture context...");
        hideError();
        startScanning(currentFacingMode, true);
    });

    function showTapToScanPrompt(msg) {
        if (msg) {
            tapPrompt.querySelector("span").textContent = msg;
        }
        tapPrompt.style.display = "flex";
    }

    function hideTapToScanPrompt() {
        tapPrompt.style.display = "none";
    }

    // File Upload event handlers
    uploadBtn.addEventListener("click", () => {
        fileInput.click();
    });

    fileInput.addEventListener("change", e => {
        if (e.target.files.length === 0) return;
        const file = e.target.files[0];
        console.log(`User uploaded file: ${file.name} (${file.size} bytes). Processing...`);

        // Step 1: Stop live camera scanning if running
        let stopPromise = Promise.resolve();
        if (html5QrScanner && html5QrScanner.isScanning) {
            stopPromise = html5QrScanner.stop();
        }

        stopPromise.then(() => {
            hideError();
            hideTapToScanPrompt();
            
            // Set laser and overlay to display none during static file scanning
            laser.style.display = 'none';
            overlay.style.display = 'none';

            // Step 2: Scan the image file
            html5QrScanner.scanFile(file, false)
                .then(decodedText => {
                    handleSuccess(decodedText);
                })
                .catch(err => {
                    console.error(`File decoding failed: ${err}`);
                    showError("No valid QR code found in this image. Please try a clearer image or use the live scanner.");
                    fileInput.value = ""; // Reset file input
                    
                    // Restart live scanning
                    startScanning(currentFacingMode, false);
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