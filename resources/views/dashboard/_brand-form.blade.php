{{-- Form tampilan halaman aktivasi; dipakai reseller (Pengaturan) dan admin (Reseller → Atur tampilan). --}}
        @php $b = \App\Support\Brand::make($owner->brand_name, $owner->brand_color, $owner->brand_logo); @endphp
        <div class="panel" x-data="brandForm(@js(['name' => $owner->brand_name ?? '', 'color' => $b['color'], 'logo' => $b['logo'], 'hasCustom' => (bool) $owner->brand_logo]))">
            <div class="panel-head">
                <div>
                    <span class="panel-title">Tampilan halaman aktivasi</span>
                    <p class="panel-sub">{{ $subtitle }}</p>
                </div>
            </div>
            <div class="panel-body brand-grid">
                <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="form-stack">
                    @csrf @method('PUT')
                    <input type="hidden" name="remove_logo" :value="removed ? 1 : 0">

                    <div class="field">
                        <label for="brand_logo">Logo</label>
                        <input type="file" id="brand_logo" name="brand_logo" accept="image/png,image/jpeg,image/webp" @change="pick($event)" class="file-input">
                        <span class="hint">PNG, JPG, atau WebP, maksimal 2 MB. PNG dengan latar transparan paling bagus.</span>
                        <button type="button" class="btn-text" x-show="hasCustom && !removed" @click="removeLogo()">Hapus logo, pakai logo Provecho</button>
                    </div>

                    <div class="field">
                        <label for="brand_name">Nama brand</label>
                        <input type="text" id="brand_name" name="brand_name" x-model="name" maxlength="40" placeholder="{{ \App\Support\Brand::DEFAULT_NAME }}">
                        <span class="hint">Tampil di bawah halaman. Kosongkan untuk memakai nama Provecho.</span>
                    </div>

                    <div class="field">
                        <label for="brand_color">Warna tombol</label>
                        <div class="color-row">
                            <input type="color" id="brand_color" name="brand_color" x-model="color">
                            <span class="mono" x-text="color.toUpperCase()"></span>
                            <button type="button" class="btn-text" x-show="color.toUpperCase() !== '{{ \App\Support\Brand::DEFAULT_COLOR }}'" @click="color = '{{ \App\Support\Brand::DEFAULT_COLOR }}'">Pakai warna Provecho</button>
                        </div>
                        <span class="hint">Warna teks tombol menyesuaikan otomatis supaya tetap terbaca.</span>
                    </div>

                    <div>
                        <button type="submit" class="btn btn-primary">Simpan tampilan</button>
                    </div>
                </form>

                <div class="preview" aria-label="Preview halaman aktivasi">
                    <span class="preview-label">Preview</span>
                    <div class="phone">
                        <img :src="logo" alt="" class="phone-logo">
                        <div class="phone-card">
                            <b>Aktifkan Kartu Anda</b>
                            <span class="phone-line"></span>
                            <span class="phone-line short"></span>
                            <span class="phone-input"></span>
                            <span class="phone-btn" :style="`background:${color};color:${textOn(color)}`">Aktifkan Kartu</span>
                        </div>
                        <span class="phone-foot" x-text="name.trim() || '{{ \App\Support\Brand::DEFAULT_NAME }}'"></span>
                    </div>
                </div>
            </div>
        </div>

@once
@push('styles')
<style>
.brand-grid { display: flex; flex-wrap: wrap; gap: 24px; align-items: flex-start; }
.brand-grid > form { flex: 1 1 300px; min-width: 0; }
.brand-grid > .preview { flex: 0 1 220px; }
.file-input { padding: 6px !important; font-size: 13px !important; cursor: pointer; }
.btn-text { align-self: flex-start; background: none; border: 0; padding: 0; font: inherit; font-size: 12.5px; font-weight: 600; color: var(--accent-dark); cursor: pointer; }
.btn-text:hover { text-decoration: underline; }
.color-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.color-row input[type=color] { width: 44px; height: 36px; padding: 2px; border: 1px solid var(--border-strong); border-radius: 8px; background: var(--surface); cursor: pointer; }
.color-row .mono { font-size: 13px; color: var(--muted); }

.preview { display: flex; flex-direction: column; gap: 8px; }
.preview-label { font-size: 11px; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: var(--faint); }
.phone {
    display: flex; flex-direction: column; align-items: center; gap: 12px;
    padding: 18px 14px 14px; border-radius: 18px; background: #F9FAFB; border: 1px solid var(--border);
}
.phone-logo { width: 56px; height: 56px; object-fit: contain; }
.phone-card { width: 100%; display: flex; flex-direction: column; gap: 7px; padding: 12px; border-radius: 10px; background: #fff; border: 1px solid var(--border); }
.phone-card b { font-size: 12.5px; }
.phone-line { height: 6px; border-radius: 3px; background: #E5E7EB; }
.phone-line.short { width: 60%; }
.phone-input { height: 22px; border-radius: 6px; border: 1px solid #D4D7DD; margin-top: 4px; }
.phone-btn { display: block; text-align: center; font-size: 11.5px; font-weight: 600; padding: 7px; border-radius: 7px; }
.phone-foot { font-size: 10px; font-weight: 600; letter-spacing: .06em; color: var(--faint); text-align: center; text-transform: uppercase; word-break: break-word; }
</style>
@endpush
@push('scripts')
<script>
function brandForm(init) {
    return {
        name: init.name, color: init.color, logo: init.logo,
        hasCustom: init.hasCustom, removed: false,
        pick(e) {
            const f = e.target.files[0];
            if (!f) return;
            this.removed = false;
            this.logo = URL.createObjectURL(f);
        },
        removeLogo() {
            this.removed = true;
            this.logo = '/img/logo.png';
            document.getElementById('brand_logo').value = '';
        },
        textOn(hex) {
            const n = parseInt(hex.slice(1), 16);
            const lum = (0.299 * (n >> 16) + 0.587 * ((n >> 8) & 255) + 0.114 * (n & 255)) / 255;
            return lum > 0.62 ? '#111827' : '#FFFFFF';
        },
    };
}
</script>
@endpush
@endonce
