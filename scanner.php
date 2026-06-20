<?php
/**
 * Lurnixe Health Card System - Live QR Scanner Registry
 * June 2026
 */
$page_title = "QR Scanner Portal";
require_once __DIR__ . '/includes/header.php';
?>

<style>
.scanner-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.035);
    overflow: hidden;
}
.scanner-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-blue-hover) 100%);
    padding: 24px;
    text-align: center;
    color: #ffffff;
}
.scanner-viewport-wrapper {
    position: relative;
    width: 100%;
    max-width: 480px;
    margin: 0 auto;
    border-radius: 12px;
    overflow: hidden;
    background: #000000;
    aspect-ratio: 1;
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
/* Scanning Box Overlay animation */
.scanner-laser {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, transparent, var(--primary-green), transparent);
    animation: scanning 2s linear infinite;
    z-index: 10;
    pointer-events: none;
}
@keyframes scanning {
    0% { top: 0%; }
    50% { top: 100%; }
    100% { top: 0%; }
}
.scanner-frame-overlay {
    position: absolute;
    top: 10%;
    left: 10%;
    right: 10%;
    bottom: 10%;
    border: 3px solid rgba(39, 174, 96, 0.8);
    border-radius: 16px;
    box-shadow: 0 0 0 9999px rgba(0, 0, 0, 0.5);
    z-index: 5;
    pointer-events: none;
}
.scanner-frame-overlay::before,
.scanner-frame-overlay::after,
.scanner-frame-overlay span::before,
.scanner-frame-overlay span::after {
    content: "";
    position: absolute;
    width: 20px;
    height: 20px;
    border-color: #ffffff;
    border-style: solid;
    z-index: 6;
}
/* Top-Left Corner */
.scanner-frame-overlay::before {
    top: -3px;
    left: -3px;
    border-width: 4px 0 0 4px;
    border-top-left-radius: 12px;
}
/* Top-Right Corner */
.scanner-frame-overlay::after {
    top: -3px;
    right: -3px;
    border-width: 4px 4px 0 0;
    border-top-right-radius: 12px;
}
/* Bottom-Left Corner */
.scanner-frame-overlay span::before {
    bottom: -3px;
    left: -3px;
    border-width: 0 0 4px 4px;
    border-bottom-left-radius: 12px;
}
/* Bottom-Right Corner */
.scanner-frame-overlay span::after {
    bottom: -3px;
    right: -3px;
    border-width: 0 4px 4px 0;
    border-bottom-right-radius: 12px;
}

.manual-entry-card {
    border-top: 1px solid var(--border-color);
}
</style>

<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6" data-aos="zoom-in">
            
            <!-- Scanner Card -->
            <div class="scanner-card mb-4">
                <div class="scanner-header">
                    <h3 class="text-white fw-bold mb-1"><i class="fa-solid fa-qrcode me-2"></i>Scan Health Card</h3>
                    <p class="text-white-50 small mb-0">Align the membership QR code within the target frame to scan</p>
                </div>
                
                <div class="card-body p-4">
                    <!-- Scanner Box -->
                    <div class="scanner-viewport-wrapper mb-4" id="scannerWrapper">
                        <div id="reader"></div>
                        <div class="scanner-laser" id="laser"></div>
                        <div class="scanner-frame-overlay" id="overlay"><span></span></div>
                    </div>
                    
                    <!-- Camera selector / Status message -->
                    <div class="mb-3">
                        <div id="scannerStatus" class="alert alert-info py-2 small mb-3">
                            <i class="fa-solid fa-spinner fa-spin me-2"></i>Initializing camera stream...
                        </div>
                        <select id="cameraSelect" class="form-select rounded-2 form-select-sm mx-auto mb-2" style="max-width: 250px; display: none;">
                            <option value="">Switch Camera</option>
                        </select>
                    </div>
                    
                    <!-- Actions -->
                    <div class="d-flex justify-content-center gap-2">
                        <button id="startScanBtn" class="btn btn-success btn-sm rounded-pill px-4" style="display: none;">
                            <i class="fa-solid fa-camera me-1"></i> Start Scanner
                        </button>
                        <button id="stopScanBtn" class="btn btn-outline-danger btn-sm rounded-pill px-4" style="display: none;">
                            <i class="fa-solid fa-camera-slash me-1"></i> Stop Camera
                        </button>
                    </div>
                </div>
                
                <!-- Fallback Manual Entry -->
                <div class="manual-entry-card p-4 bg-light text-start">
                    <h6 class="text-dark fw-bold mb-3 small text-uppercase" style="letter-spacing: 0.5px;">
                        <i class="fa-solid fa-keyboard text-success me-2"></i>Camera Denied? Verify Manually
                    </h6>
                    <form id="manualVerifyForm">
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-id-card text-muted"></i></span>
                            <input type="text" class="form-control border-start-0 rounded-end-2 font-code fw-bold text-uppercase" id="manualMemberId" placeholder="e.g. LFC000124" required style="letter-spacing: 0.5px;">
                            <button type="submit" class="btn btn-primary px-4"><i class="fa-solid fa-magnifying-glass me-1"></i> Search</button>
                        </div>
                        <div id="manualError" class="text-danger small mt-2 fw-semibold" style="display: none;"></div>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</div>

<!-- Load html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const readerDiv = document.getElementById("reader");
    const statusDiv = document.getElementById("scannerStatus");
    const cameraSelect = document.getElementById("cameraSelect");
    const startBtn = document.getElementById("startScanBtn");
    const stopBtn = document.getElementById("stopScanBtn");
    const laser = document.getElementById("laser");
    const overlay = document.getElementById("overlay");
    
    let html5QrScanner = null;
    let cameraList = [];
    let activeCameraId = null;

    // Check if CDN loaded
    if (typeof Html5Qrcode === "undefined") {
        statusDiv.className = "alert alert-danger py-2 small";
        statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>Failed to load scanner library. Reload page.';
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
            statusDiv.className = "alert alert-success py-2 small";
            statusDiv.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>Camera found. Click Start to begin scan.';
            
            // Populate select dropdown
            cameraSelect.innerHTML = "";
            devices.forEach((device, index) => {
                const opt = document.createElement("option");
                opt.value = device.id;
                opt.text = device.label || `Camera ${index + 1}`;
                cameraSelect.appendChild(opt);
            });
            cameraSelect.style.display = "inline-block";
            startBtn.style.display = "inline-block";

            // Try to auto-start with back camera
            let backCam = devices.find(device => device.label.toLowerCase().includes("back") || device.label.toLowerCase().includes("environment"));
            let selectedCamId = backCam ? backCam.id : devices[0].id;
            
            cameraSelect.value = selectedCamId;
            startScanning(selectedCamId);
        } else {
            statusDiv.className = "alert alert-warning py-2 small";
            statusDiv.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>No camera device detected.';
            laser.style.display = 'none';
            overlay.style.display = 'none';
        }
    }).catch(err => {
        console.error("Camera access failed", err);
        statusDiv.className = "alert alert-danger py-2 small";
        statusDiv.innerHTML = '<i class="fa-solid fa-ban me-2"></i>Camera permission denied or camera block error.';
        laser.style.display = 'none';
        overlay.style.display = 'none';
    });

    // Start scan function
    function startScanning(cameraId) {
        statusDiv.className = "alert alert-info py-2 small";
        statusDiv.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Accessing camera stream...';
        laser.style.display = 'block';
        overlay.style.display = 'block';

        html5QrScanner.start(
            cameraId,
            {
                fps: 15,
                qrbox: (width, height) => {
                    // Make it fill 80% of width/height
                    return { width: width * 0.8, height: height * 0.8 };
                },
                aspectRatio: 1.0
            },
            onScanSuccess,
            onScanFailure
        ).then(() => {
            activeCameraId = cameraId;
            statusDiv.className = "alert alert-success py-2 small";
            statusDiv.innerHTML = '<i class="fa-solid fa-video me-2"></i>Camera active. Scanning...';
            startBtn.style.display = "none";
            stopBtn.style.display = "inline-block";
        }).catch(err => {
            console.error("Failed to start scanner", err);
            statusDiv.className = "alert alert-danger py-2 small";
            statusDiv.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-2"></i>Failed to start: ${err}`;
            laser.style.display = 'none';
            overlay.style.display = 'none';
        });
    }

    // Stop scan function
    function stopScanning() {
        if (html5QrScanner && html5QrScanner.isScanning) {
            html5QrScanner.stop().then(() => {
                statusDiv.className = "alert alert-warning py-2 small";
                statusDiv.innerHTML = '<i class="fa-solid fa-circle-stop me-2"></i>Camera stopped.';
                startBtn.style.display = "inline-block";
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
        
        statusDiv.className = "alert alert-success py-2 small fw-bold";
        statusDiv.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>QR Code detected! Redirecting...';

        // Check if QR matches member profile URL pattern
        // Example URL: http://localhost/LurnixeHealth/member.php?id=LFC000124
        // Or could be just raw ID if parsed differently
        let redirectUrl = decodedText;
        if (decodedText.includes("member.php?id=")) {
            window.location.href = redirectUrl;
        } else {
            // Treat as raw ID fallback
            window.location.href = `member.php?id=${encodeURIComponent(decodedText)}`;
        }
    }

    // On Failure (silent - it fires on every frame without match)
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
