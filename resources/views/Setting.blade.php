@extends('layout.app')

@section('title', 'Cash.Flow | Setelan')

@section('content')
@php
    $user = auth()->user();
    $activeTab = session('active_tab');

    if (!$activeTab) {
        $activeTab = $errors->has('current_password') || $errors->has('password') || $errors->has('password_confirmation')
            ? 'security'
            : 'account';
    }

    $selectedCountryCode = old('phone_country_code', $user->phone_country_code ?? '');
    $selectedPhoneNumber = old('phone_number', $user->phone_number ?? '');
    $profilePhotoUrl = $user->profile_photo_url;
@endphp

<style>
    .settings-page {
        display: grid;
        gap: 20px;
    }

    .settings-hero {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        padding: 6px 4px 0;
    }

    .settings-title {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
        letter-spacing: -0.03em;
    }

    .settings-subtitle {
        margin: 8px 0 0;
        color: #6B7280;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .settings-shell {
        display: grid;
        gap: 18px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 30px;
        box-shadow: 0 22px 50px rgba(15, 23, 42, 0.06);
        padding: 22px;
        min-height: 560px;
    }

    .settings-tabs {
        display: inline-flex;
        gap: 24px;
        align-items: center;
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 14px;
    }

    .settings-tab {
        border: none;
        background: transparent;
        padding: 0;
        font-size: 1rem;
        font-weight: 700;
        color: #6B7280;
        cursor: pointer;
        position: relative;
    }

    .settings-tab.active {
        color: #299D91;
    }

    .settings-tab.active::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -16px;
        height: 3px;
        border-radius: 999px;
        background: #299D91;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel.active {
        display: block;
    }

    .account-layout {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(280px, 0.8fr);
        gap: 20px;
        align-items: start;
    }

    .form-grid {
        display: grid;
        gap: 12px;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .field-group {
        display: grid;
        gap: 7px;
        padding: 12px 14px;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        background: #FFFFFF;
    }

    .field-label {
        font-size: 0.93rem;
        font-weight: 700;
        color: #374151;
    }

    .field-input,
    .field-select,
    .field-textarea {
        width: 100%;
        border: 1px solid #E2E8F0;
        background: #FFFFFF;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.92rem;
        color: #111827;
        outline: none;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .field-input:focus,
    .field-select:focus,
    .field-textarea:focus {
        border-color: #299D91;
        box-shadow: 0 0 0 3px rgba(41, 157, 145, 0.10);
    }

    .field-help {
        font-size: 0.86rem;
        color: #6B7280;
    }

    .phone-row {
        display: grid;
        grid-template-columns: 170px minmax(0, 1fr);
        gap: 8px;
    }

    .field-group .phone-row {
        margin-top: 0;
    }

    .field-group .field-help {
        margin-top: 0;
    }

    .photo-panel {
        display: grid;
        gap: 16px;
        align-content: start;
    }

    .photo-card,
    .summary-card,
    .security-card {
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        background: #FFFFFF;
        padding: 14px;
    }

    .photo-card h3,
    .summary-card h3,
    .security-card h3 {
        margin: 0 0 14px;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }

    .photo-dropzone {
        display: grid;
        place-items: center;
        gap: 10px;
        min-height: 150px;
        border: 2px dashed #D1D5DB;
        border-radius: 16px;
        background: #FAFAFA;
        padding: 14px;
        text-align: center;
        cursor: pointer;
    }

    .photo-preview {
        width: 88px;
        height: 88px;
        border-radius: 22px;
        background: linear-gradient(135deg, #299D91 0%, #1F6F66 100%);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #FFFFFF;
        font-size: 1.15rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(41, 157, 145, 0.12);
        overflow: hidden;
        position: relative;
        flex: 0 0 88px;
    }

    .photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .photo-preview span {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .photo-preview.has-photo {
        color: transparent;
    }

    .photo-preview.has-photo .preview-initials {
        display: none;
    }

    .photo-preview.has-photo .preview-image {
        display: block;
    }

    .preview-image {
        display: none;
        object-fit: cover;
    }

    .photo-meta {
        display: grid;
        gap: 4px;
        color: #6B7280;
        font-size: 0.9rem;
    }

    .phone-summary {
        display: grid;
        gap: 4px;
        padding: 12px 14px;
        border-radius: 14px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
    }

    .phone-summary span {
        font-size: 0.84rem;
        color: #6B7280;
    }

    .phone-summary strong {
        font-size: 0.98rem;
        color: #111827;
    }

    .button-row {
        display: flex;
        gap: 12px;
        align-items: center;
        margin-top: 2px;
    }

    .button-primary {
        border: none;
        border-radius: 12px;
        padding: 11px 18px;
        background: #299D91;
        color: #FFFFFF;
        font-size: 0.94rem;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 10px 18px rgba(41, 157, 145, 0.14);
    }

    .button-secondary {
        border: 1px solid #D1D5DB;
        border-radius: 12px;
        padding: 11px 18px;
        background: #FFFFFF;
        color: #374151;
        font-size: 0.94rem;
        font-weight: 700;
        cursor: pointer;
    }

    .security-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(260px, 0.72fr);
        gap: 20px;
        align-items: start;
    }

    .security-form {
        display: grid;
        gap: 12px;
        max-width: 470px;
    }

    .password-box {
        display: grid;
        gap: 12px;
        padding: 14px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
    }

    .password-box .field-input,
    .password-box .field-select,
    .password-box .field-textarea {
        background: #FAFAFA;
        border-color: #E5E7EB;
    }

    .password-box .field-input:focus,
    .password-box .field-select:focus,
    .password-box .field-textarea:focus {
        background: #FFFFFF;
    }

    .notification-card {
        border-radius: 18px;
        padding: 16px 18px;
        border: 1px solid transparent;
    }

    .notification-card.success {
        background: #ECFDF5;
        border-color: #A7F3D0;
        color: #065F46;
    }

    .notification-card.error {
        background: #FEF2F2;
        border-color: #FECACA;
        color: #991B1B;
    }

    .validation-list {
        margin: 0;
        padding-left: 18px;
    }

    @media (max-width: 1024px) {
        .account-layout,
        .security-layout,
        .form-row {
            grid-template-columns: 1fr;
        }

        .phone-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="settings-page">
    <div class="settings-hero">
        <div>
            <h1 class="settings-title">Setelan Akun</h1>
            <p class="settings-subtitle">Kelola nama, email, nomor telepon, foto profil, dan password Anda dari satu tempat.</p>
        </div>
    </div>

    @if(session('status') || session('error'))
        <div class="notification-card {{ session('error') ? 'error' : 'success' }}">
            <strong>{{ session('error') ? 'Terjadi masalah' : 'Berhasil' }}</strong>
            <div>{{ session('status') ?? session('error') }}</div>
        </div>
    @endif

    @if($errors->any())
        <div class="notification-card error">
            <strong>Periksa kembali isian Anda</strong>
            <ul class="validation-list">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="settings-shell">
        <div class="settings-tabs" role="tablist" aria-label="Pengaturan akun">
            <button type="button" class="settings-tab {{ $activeTab === 'account' ? 'active' : '' }}" data-tab="account">Akun</button>
            <button type="button" class="settings-tab {{ $activeTab === 'security' ? 'active' : '' }}" data-tab="security">Keamanan</button>
        </div>

        <section class="tab-panel {{ $activeTab === 'account' ? 'active' : '' }}" data-panel="account">
            <div class="account-layout">
                <form id="account-settings-form" class="form-grid" method="POST" action="{{ route('setelan.account') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-row">
                        <div class="field-group">
                            <label for="name" class="field-label">Nama Panjang</label>
                            <input id="name" name="name" type="text" class="field-input" value="{{ old('name', $user->name) }}" placeholder="Nama lengkap Anda">
                        </div>

                        <div class="field-group">
                            <label for="email" class="field-label">Email</label>
                            <input id="email" name="email" type="email" class="field-input" value="{{ old('email', $user->email) }}" placeholder="email@domain.com">
                        </div>
                    </div>

                    <div class="field-group">
                        <label for="phone_number" class="field-label">Nomor Telepon</label>
                        <div class="phone-row">
                            <select id="phone_country_code" name="phone_country_code" class="field-select">
                                <option value="">Pilih kode negara</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country['code'] }}" {{ $selectedCountryCode === $country['code'] ? 'selected' : '' }}>{{ $country['label'] }}</option>
                                @endforeach
                            </select>
                            <input id="phone_number" name="phone_number" type="text" class="field-input" value="{{ $selectedPhoneNumber }}" placeholder="81234567890">
                        </div>
                        <div class="field-help">Jika belum diisi, akun akan menampilkan Tidak Ada.</div>
                    </div>

                    <div class="button-row">
                        <button type="submit" class="button-primary">Simpan Perubahan</button>
                        <a href="{{ route('beranda') }}" class="button-secondary" style="text-decoration:none; display:inline-flex; align-items:center; justify-content:center;">Kembali</a>
                    </div>
                </form>

                <div class="photo-panel">
                    <div class="photo-card">
                        <h3>Foto Profil</h3>
                        <label class="photo-dropzone" for="profile_photo">
                            <div class="photo-preview {{ $profilePhotoUrl ? 'has-photo' : '' }}" id="photo-preview">
                                <img
                                    id="photo-preview-image"
                                    class="preview-image"
                                    src="{{ $profilePhotoUrl ?? '' }}"
                                    alt="Preview foto profil"
                                    @if(!$profilePhotoUrl) style="display:none;" @endif
                                    onerror="this.style.display='none';document.getElementById('photo-preview-initials').style.display='flex';"
                                >
                                <span id="photo-preview-initials" class="preview-initials" @if($profilePhotoUrl) style="display:none;" @endif>{{ $user->profile_initials }}</span>
                            </div>
                            <div class="photo-meta">
                                <strong style="color:#111827; font-size:1rem;">{{ $profilePhotoUrl ? 'Ganti foto profil' : 'Upload foto profil' }}</strong>
                                <span>Format JPG, PNG, atau WEBP. Maksimal 2 MB.</span>
                            </div>
                        </label>
                        <input id="profile_photo" name="profile_photo" type="file" class="field-input" form="account-settings-form" style="display:none;" accept="image/jpeg,image/png,image/webp">
                        <div class="field-help" style="margin-top:12px;">Foto ini akan ikut tampil di navbar dan area profil lain.</div>
                        @if($profilePhotoUrl)
                            <form method="POST" action="{{ route('setelan.photo.remove') }}" style="margin-top:12px;">
                                @csrf
                                <button type="submit" class="button-secondary" style="width:100%;">Hapus Foto Profil</button>
                            </form>
                        @endif
                    </div>

                    <div class="summary-card">
                        <h3>Ringkasan Akun</h3>
                        <div class="phone-summary">
                            <span>Nama</span>
                            <strong>{{ $user->name }}</strong>
                        </div>
                        <div class="phone-summary" style="margin-top:12px;">
                            <span>Email</span>
                            <strong>{{ $user->email }}</strong>
                        </div>
                        <div class="phone-summary" style="margin-top:12px;">
                            <span>Nomor Telepon</span>
                            <strong>{{ $user->phone_display }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="tab-panel {{ $activeTab === 'security' ? 'active' : '' }}" data-panel="security">
            <div class="security-layout">
                <form class="security-form" method="POST" action="{{ route('setelan.security') }}">
                    @csrf

                    <div class="password-box">
                        <div class="field-group">
                            <label for="current_password" class="field-label">Password Lama</label>
                            <input id="current_password" name="current_password" type="password" class="field-input" placeholder="Masukkan password lama">
                        </div>

                        <div class="field-group">
                            <label for="password" class="field-label">Password Baru</label>
                            <input id="password" name="password" type="password" class="field-input" placeholder="Buat password baru">
                        </div>

                        <div class="field-group">
                            <label for="password_confirmation" class="field-label">Ketik Ulang Password Baru</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" class="field-input" placeholder="Ulangi password baru">
                        </div>

                        <div class="field-group">
                            <label for="security_phone_note" class="field-label">Nomor Telepon</label>
                            <input id="security_phone_note" type="text" class="field-input" value="{{ $user->phone_display }}" readonly>
                            <div class="field-help">Bagian ini tetap sinkron dengan tab Akun. Jika ingin menambah atau mengganti nomor, lakukan dari sana.</div>
                        </div>

                        <div class="button-row">
                            <button type="submit" class="button-primary">Simpan</button>
                        </div>
                    </div>
                </form>

                <div class="security-card">
                    <h3>Aturan Keamanan</h3>
                    <div class="phone-summary" style="margin-bottom:12px;">
                        <span>Password lama harus cocok dengan password yang saat ini tersimpan di database.</span>
                    </div>
                    <div class="phone-summary" style="margin-bottom:12px;">
                        <span>Nomor telepon bersifat opsional dan hanya disimpan jika kode negara dan nomor diisi bersama.</span>
                    </div>
                    <div class="phone-summary">
                        <span>Setelah password diubah, Anda tetap login pada sesi aktif ini.</span>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tabs = document.querySelectorAll('.settings-tab');
        const panels = document.querySelectorAll('.tab-panel');
        const photoInput = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('photo-preview');
        const photoPreviewImage = document.getElementById('photo-preview-image');
        const photoPreviewInitials = document.getElementById('photo-preview-initials');

        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                const target = tab.getAttribute('data-tab');

                tabs.forEach(function (item) {
                    item.classList.toggle('active', item === tab);
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('active', panel.getAttribute('data-panel') === target);
                });
            });
        });

        if (photoInput && photoPreview && photoPreviewImage && photoPreviewInitials) {
            photoInput.addEventListener('change', function () {
                const file = photoInput.files && photoInput.files[0];

                if (!file) {
                    return;
                }

                const objectUrl = URL.createObjectURL(file);
                const image = new Image();
                image.onload = function () {
                    const cropSize = Math.min(image.width, image.height);
                    const sourceX = Math.floor((image.width - cropSize) / 2);
                    const sourceY = Math.floor((image.height - cropSize) / 2);
                    const targetSize = 1024;

                    const canvas = document.createElement('canvas');
                    canvas.width = targetSize;
                    canvas.height = targetSize;

                    const context = canvas.getContext('2d');
                    if (!context) {
                        URL.revokeObjectURL(objectUrl);
                        return;
                    }

                    context.drawImage(image, sourceX, sourceY, cropSize, cropSize, 0, 0, targetSize, targetSize);

                    canvas.toBlob(function (blob) {
                        URL.revokeObjectURL(objectUrl);

                        if (!blob) {
                            return;
                        }

                        const croppedFile = new File([blob], file.name.replace(/\.[^.]+$/, '') + '.png', { type: 'image/png' });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(croppedFile);
                        photoInput.files = dataTransfer.files;

                        const previewUrl = URL.createObjectURL(blob);
                        photoPreview.classList.add('has-photo');
                        photoPreviewImage.src = previewUrl;
                        photoPreviewImage.style.display = 'block';
                        photoPreviewInitials.style.display = 'none';

                        photoPreviewImage.onload = function () {
                            URL.revokeObjectURL(previewUrl);
                        };
                    }, 'image/png', 0.95);
                };

                image.src = objectUrl;
            });
        }
    });
</script>
@endsection
