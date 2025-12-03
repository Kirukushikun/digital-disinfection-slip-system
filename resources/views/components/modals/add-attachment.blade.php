@props(['show'])

<x-modals.modal-template :show="$show" title="Add Attachment" max-width="max-w-2xl">

    <div class="flex flex-col items-center p-4 space-y-4">
        <!-- Camera Preview (Square) -->
        <div class="relative w-80 h-80 bg-gray-900 rounded-lg overflow-hidden">
            <video id="cameraPreview" class="w-full h-full object-cover" autoplay playsinline></video>
            <canvas id="photoCanvas" class="hidden"></canvas>
        </div>

        <!-- Captured Photo Preview -->
        <div id="capturedPhotoContainer" class="hidden w-80 h-80">
            <img id="capturedPhoto" class="w-full h-full object-cover rounded-lg" alt="Captured photo">
        </div>

        <!-- Status Message -->
        <p id="statusMessage" class="text-sm text-gray-600"></p>
    </div>

    <x-slot name="footer">
        <!-- Cancel Button (Always visible) -->
        <x-buttons.submit-button wire:click="closeAddAttachmentModal" color="white">
            Cancel
        </x-buttons.submit-button>

        <!-- Capture/Retake Button -->
        <x-buttons.submit-button id="captureBtn" onclick="capturePhoto()" color="blue">
            Capture Photo
        </x-buttons.submit-button>

        <x-buttons.submit-button id="retakeBtn" onclick="retakePhoto()" color="gray" class="hidden">
            Retake
        </x-buttons.submit-button>

        <!-- Upload Button (Hidden until photo is captured) -->
        <x-buttons.submit-button id="uploadBtn" onclick="uploadCapturedPhoto()" color="green" class="hidden">
            Upload
        </x-buttons.submit-button>
    </x-slot>

    @push('scripts')
        <script>
            let stream = null;
            let capturedImageData = null;

            const video = document.getElementById('cameraPreview');
            const canvas = document.getElementById('photoCanvas');
            const capturedPhoto = document.getElementById('capturedPhoto');
            const capturedPhotoContainer = document.getElementById('capturedPhotoContainer');
            const captureBtn = document.getElementById('captureBtn');
            const retakeBtn = document.getElementById('retakeBtn');
            const uploadBtn = document.getElementById('uploadBtn');
            const statusMessage = document.getElementById('statusMessage');

            // Start camera when modal opens
            document.addEventListener('livewire:init', () => {
                Livewire.on('showAddAttachmentModal', () => {
                    startCamera();
                });

                Livewire.hook('message.processed', () => {
                    if (!@this.showAddAttachmentModal) {
                        stopCamera();
                        resetCamera();
                    }
                });
            });

            async function startCamera() {
                try {
                    stream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: 'environment', // Back camera
                            width: {
                                ideal: 640
                            },
                            height: {
                                ideal: 480
                            }
                        }
                    });

                    video.srcObject = stream;
                    video.parentElement.classList.remove('hidden');
                    capturedPhotoContainer.classList.add('hidden');
                    statusMessage.textContent = 'Camera ready';
                    statusMessage.classList.remove('text-red-600');
                    statusMessage.classList.add('text-gray-600');
                } catch (error) {
                    statusMessage.textContent = 'Error accessing camera: ' + error.message;
                    statusMessage.classList.remove('text-gray-600');
                    statusMessage.classList.add('text-red-600');
                    console.error('Camera error:', error);
                }
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                    stream = null;
                }
            }

            function capturePhoto() {
                const context = canvas.getContext('2d');

                // Set canvas to square dimensions
                canvas.width = 640;
                canvas.height = 640;

                // Draw the video frame to canvas
                context.drawImage(video, 0, 0, canvas.width, canvas.height);

                // Convert to data URL (base64)
                const imageDataUrl = canvas.toDataURL('image/jpeg', 0.85);

                // Check size
                const sizeInBytes = Math.round((imageDataUrl.length - 'data:image/jpeg;base64,'.length) * 0.75);
                const sizeInMB = sizeInBytes / (1024 * 1024);

                if (sizeInMB > 15) {
                    // Compress more if too large
                    const compressedImageData = canvas.toDataURL('image/jpeg', 0.7);
                    const compressedSize = Math.round((compressedImageData.length - 'data:image/jpeg;base64,'.length) * 0.75);
                    const compressedSizeMB = compressedSize / (1024 * 1024);

                    if (compressedSizeMB > 15) {
                        statusMessage.textContent = 'Image too large even after compression. Please try again.';
                        statusMessage.classList.remove('text-gray-600');
                        statusMessage.classList.add('text-red-600');
                        return;
                    }

                    capturedImageData = compressedImageData;
                    displayCapturedPhoto(compressedImageData, compressedSizeMB);
                } else {
                    capturedImageData = imageDataUrl;
                    displayCapturedPhoto(imageDataUrl, sizeInMB);
                }
            }

            function displayCapturedPhoto(imageDataUrl, sizeMB) {
                capturedPhoto.src = imageDataUrl;

                // Hide camera, show captured photo
                video.parentElement.classList.add('hidden');
                capturedPhotoContainer.classList.remove('hidden');

                // Toggle buttons
                captureBtn.classList.add('hidden');
                retakeBtn.classList.remove('hidden');
                uploadBtn.classList.remove('hidden');

                stopCamera();

                statusMessage.textContent = `Photo captured (${sizeMB.toFixed(2)} MB)`;
                statusMessage.classList.remove('text-red-600');
                statusMessage.classList.add('text-gray-600');
            }

            function retakePhoto() {
                // Hide captured photo, show camera
                capturedPhotoContainer.classList.add('hidden');

                // Toggle buttons
                retakeBtn.classList.add('hidden');
                uploadBtn.classList.add('hidden');
                captureBtn.classList.remove('hidden');

                capturedImageData = null;
                startCamera();
            }

            function resetCamera() {
                capturedPhotoContainer.classList.add('hidden');
                retakeBtn.classList.add('hidden');
                uploadBtn.classList.add('hidden');
                captureBtn.classList.remove('hidden');
                capturedImageData = null;
                statusMessage.textContent = '';
            }

            function uploadCapturedPhoto() {
                if (!capturedImageData) {
                    statusMessage.textContent = 'No photo captured. Please capture a photo first.';
                    statusMessage.classList.remove('text-gray-600');
                    statusMessage.classList.add('text-red-600');
                    return;
                }

                // Disable upload button to prevent double clicks
                uploadBtn.disabled = true;
                statusMessage.textContent = 'Uploading...';
                statusMessage.classList.remove('text-red-600');
                statusMessage.classList.add('text-gray-600');

                // Send image data to Livewire
                @this.uploadAttachment(capturedImageData)
                    .then(() => {
                        // Reset on success
                        resetCamera();
                    })
                    .catch((error) => {
                        console.error('Upload error:', error);
                        statusMessage.textContent = 'Upload failed. Please try again.';
                        statusMessage.classList.remove('text-gray-600');
                        statusMessage.classList.add('text-red-600');
                    })
                    .finally(() => {
                        uploadBtn.disabled = false;
                    });
            }
        </script>
    @endpush

</x-modals.modal-template>
