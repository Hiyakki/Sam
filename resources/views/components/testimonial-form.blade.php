<div x-data="{ open: false, isSubmitting: false }" @keydown.escape.window="open = false">
    <!-- CTA Button -->
    <button @click="open = true" class="inline-flex items-center justify-center px-6 py-3 rounded-full text-sm font-semibold text-white bg-primary hover:bg-primary-dark transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5 active:translate-y-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
        Tulis Ulasan Anda
    </button>

    <!-- Modal Backdrop -->
    <div x-show="open" x-transition.opacity class="fixed inset-0 bg-black/60 z-50 backdrop-blur-sm" style="display: none;"></div>

    <!-- Modal Content -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6" style="display: none;">
         
        <div @click.away="open = false" class="bg-white w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl shadow-2xl relative">
            <!-- Close Button -->
            <button @click="open = false" type="button" class="absolute top-4 right-4 sm:top-6 sm:right-6 p-2 rounded-full text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors focus:outline-hidden focus:ring-2 focus:ring-primary">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="p-6 sm:p-10">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold font-['Playfair_Display'] text-primary mb-2">Bagikan Pengalaman Anda</h2>
                    <p class="text-sm text-gray-500">Ulasan Anda sangat berarti bagi kami dan pasien lainnya.</p>
                </div>

                <form action="{{ route('testimonials.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
                    @csrf
                    
                    <!-- Honeypot -->
                    <div style="display:none;">
                        <input type="text" name="website_url" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap <span class="text-red-500">*</span></label>
                            <input type="text" id="name" name="name" required maxlength="100" value="{{ old('name') }}" 
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-shadow">
                            @error('name') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label for="role_or_info" class="block text-sm font-semibold text-gray-700 mb-1.5">Keterangan / Status</label>
                            <input type="text" id="role_or_info" name="role_or_info" maxlength="150" value="{{ old('role_or_info') }}" placeholder="Cth: Pengguna Kaki Palsu" 
                                class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-shadow">
                            @error('role_or_info') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <!-- Interactive Star Rating -->
                    <div x-data="{ rating: {{ old('rating', 5) }}, hoverRating: 0 }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Bintang Penilaian <span class="text-red-500">*</span></label>
                        <div class="flex items-center gap-2">
                            <template x-for="i in 5">
                                <button type="button" 
                                        @click="rating = i" 
                                        @mouseenter="hoverRating = i" 
                                        @mouseleave="hoverRating = 0"
                                        class="focus:outline-hidden focus:ring-2 focus:ring-primary rounded-full p-1 transition-transform hover:scale-110">
                                    <svg class="w-8 h-8" :class="(hoverRating >= i || (!hoverRating && rating >= i)) ? 'text-amber-400' : 'text-gray-200'" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </button>
                            </template>
                            <input type="hidden" name="rating" x-model="rating">
                        </div>
                        @error('rating') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-semibold text-gray-700 mb-1.5">Ceritakan Pengalaman Anda <span class="text-red-500">*</span></label>
                        <textarea id="content" name="content" required minlength="20" maxlength="1000" rows="4" 
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:ring-2 focus:ring-primary focus:border-primary transition-shadow placeholder:text-gray-400"
                            placeholder="Bagaimana pelayanan yang Anda dapatkan? Bagaimana kualitas alat yang dibuat? (Min. 20 karakter)">{{ old('content') }}</textarea>
                        @error('content') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div x-data="{ files: [], dragOver: false }" 
                         @dragover.prevent="dragOver = true" 
                         @dragleave.prevent="dragOver = false" 
                         @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Foto (Maksimal 2, Opsional)</label>
                        
                        <div class="relative border-2 border-dashed rounded-2xl p-6 text-center transition-colors"
                             :class="dragOver ? 'border-primary bg-primary/5' : 'border-gray-200 bg-gray-50/50 hover:bg-gray-50'">
                            
                            <input type="file" id="photos" name="photos[]" accept="image/jpeg,image/png,image/webp" multiple max="2"
                                   x-ref="fileInput"
                                   @change="files = Array.from($event.target.files).slice(0, 2)"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer rounded-2xl">
                            
                            <div x-show="files.length === 0" class="pointer-events-none">
                                <svg class="w-10 h-10 mx-auto text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="text-sm font-medium text-gray-700">Klik atau seret foto ke sini</p>
                                <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG, WebP (Maks. 2MB)</p>
                            </div>
                            
                            <!-- File Preview List -->
                            <div x-show="files.length > 0" class="flex flex-wrap gap-3 justify-center pointer-events-none">
                                <template x-for="(file, index) in files" :key="index">
                                    <div class="bg-white border border-gray-200 px-3 py-2 rounded-lg text-sm flex items-center gap-2">
                                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        <span class="truncate max-w-[150px] font-medium" x-text="file.name"></span>
                                    </div>
                                </template>
                            </div>
                        </div>
                        @error('photos') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                        @error('photos.*') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex items-start gap-3">
                        <div class="mt-0.5">
                            <input type="checkbox" id="consent_confirmed" name="consent_confirmed" required value="1" 
                                class="w-5 h-5 rounded border-gray-300 text-primary focus:ring-primary cursor-pointer">
                        </div>
                        <div>
                            <label for="consent_confirmed" class="text-sm text-gray-700 cursor-pointer block leading-relaxed font-medium">
                                Saya mengizinkan ulasan dan foto (jika ada) ditampilkan di website SAM.
                            </label>
                            <p class="text-xs text-gray-500 mt-1">
                                Ulasan akan ditinjau terlebih dahulu oleh tim kami sebelum ditayangkan. Baca <a href="{{ route('privacy') }}" class="text-primary hover:underline">Kebijakan Privasi</a>.
                            </p>
                        </div>
                        @error('consent_confirmed') <span class="text-xs text-red-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-2 flex justify-end gap-3">
                        <button type="button" @click="open = false" class="px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-900 bg-gray-100 hover:bg-gray-200 rounded-xl transition-colors">
                            Batal
                        </button>
                        <button type="submit" 
                            :disabled="isSubmitting"
                            class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-primary hover:bg-primary-dark rounded-xl transition-colors disabled:opacity-70 disabled:cursor-not-allowed">
                            <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="isSubmitting ? 'Mengirim...' : 'Kirim Ulasan'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
