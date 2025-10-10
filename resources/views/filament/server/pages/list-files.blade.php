<x-filament-panels::page>
    <div 
        x-data="dragDropUpload" 
        x-init="init"
        @dragover.prevent="handleDragOver"
        @dragenter.prevent="handleDragEnter"
        @dragleave.prevent="handleDragLeave"
        @drop.prevent="handleDrop"
        class="relative"
        data-max-size="{{ $this->getMaxUploadSize() }}"
    >
        <div 
            x-show="isDragOver" 
            class="fixed z-50 bg-black bg-opacity-50"
            style="top: 0; left: 0; width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(4px);"
        >
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl p-8 text-center max-w-md mx-4 border-2 border-dashed border-primary-500">
                <div class="w-16 h-16 mx-auto mb-4 flex items-center justify-center bg-primary-100 dark:bg-primary-900 rounded-full">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Drop files to upload</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Release to upload to current directory</p>
            </div>
        </div>

        {{ $this->table }}
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dragDropUpload', () => ({
                isDragOver: false,
                dragCounter: 0,

                init() {
                    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                        document.addEventListener(eventName, (e) => {
                            if (e.dataTransfer && e.dataTransfer.types && e.dataTransfer.types.includes('Files')) {
                                e.preventDefault();
                                e.stopPropagation();
                            }
                        }, false);
                    });
                },

                handleDragEnter(e) {
                    if (!e.dataTransfer.types || !e.dataTransfer.types.includes('Files')) {
                        return;
                    }
                    this.dragCounter++;
                    this.isDragOver = true;
                },

                handleDragOver(e) {
                    if (!e.dataTransfer.types || !e.dataTransfer.types.includes('Files')) {
                        return;
                    }
                    e.dataTransfer.dropEffect = 'copy';
                    this.isDragOver = true;
                },

                handleDragLeave(e) {
                    if (!e.dataTransfer.types || !e.dataTransfer.types.includes('Files')) {
                        return;
                    }
                    this.dragCounter--;
                    if (this.dragCounter <= 0) {
                        this.isDragOver = false;
                        this.dragCounter = 0;
                    }
                },

                async handleDrop(e) {
                    if (!e.dataTransfer.types || !e.dataTransfer.types.includes('Files')) {
                        return;
                    }
                    this.isDragOver = false;
                    this.dragCounter = 0;

                    const files = Array.from(e.dataTransfer.files);
                    
                    if (files.length === 0) {
                        return;
                    }

                    try {
                        await this.uploadFiles(files);
                    } catch (error) {
                        console.error('Upload failed:', error);
                    }
                },

                isModalOpen() {
                    return document.querySelector('[role="dialog"]') !== null ||
                           document.querySelector('.fi-modal-open') !== null ||
                           document.querySelector('.fi-modal') !== null ||
                           document.body.classList.contains('overflow-hidden');
                },

                async uploadFiles(files) {
                    const maxSize = parseInt(document.querySelector('[x-data="dragDropUpload"]').dataset.maxSize || '0');
                    
                    const validFiles = [];
                    
                    for (const file of files) {
                        if (file.size > maxSize) {
                            this.$wire.call('showFileTooLargeNotification', file.name, Math.round(maxSize / 1024 / 1024 * 10) / 10);
                            continue;
                        }
                        validFiles.push(file);
                    }
                    
                    if (validFiles.length === 0) {
                        return;
                    }
                    
                    const uploadPromises = validFiles.map(file => {
                        return new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                resolve({
                                    name: file.name,
                                    content: e.target.result.split(',')[1],
                                    size: file.size
                                });
                            };
                            reader.onerror = () => reject(new Error(`Failed to read ${file.name}`));
                            reader.readAsDataURL(file);
                        });
                    });

                    try {
                        const fileData = await Promise.all(uploadPromises);
                        this.$wire.call('handleFileUpload', fileData);
                    } catch (error) {
                        console.error('Failed to process files:', error);
                    }
                }
            }));
        });
    </script>
</x-filament-panels::page>