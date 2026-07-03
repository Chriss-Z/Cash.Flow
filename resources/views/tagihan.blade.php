@extends('layout.app')

@section('title', 'Cash.Flow | Tagihan')

@section('content')

<div class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Tagihan</h1>
        <div class="topbar-meta">
            <span>{{ now()->format('M d, Y') }}</span>
            <span class="meta-separator">•</span>
            <span>Kelola semua langganan dan tagihan rutin Anda.</span>
        </div>
    </div>
    <div class="topbar-right" style="gap: 12px;">
        <div class="notification-trigger">
            <button type="button" class="icon-btn" aria-label="Notifications" id="topbar-notification-button">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M13.73 21a2 2 0 01-3.46 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                @if(count($notifications ?? []))
                    <span class="notification-badge">{{ count($notifications) }}</span>
                @endif
            </button>
            <div class="notification-menu" id="notification-menu" hidden>
                <div class="notification-menu-header">
                    <strong>Notifikasi</strong>
                </div>
                @forelse($notifications ?? [] as $notification)
                    <div class="notification-row">{{ $notification }}</div>
                @empty
                    <div class="notification-row empty">Tidak ada notifikasi baru.</div>
                @endforelse
            </div>
        </div>
        <a href="{{ route('saldo') }}" class="button-link" style="padding: 12px 18px; border-radius: 14px; border: 1px solid #E5E7EB; background: #FFFFFF;">Kembali ke Saldo</a>
        <button type="button" class="button-primary" id="open-bill-modal">+ Tambah Tagihan</button>
    </div>
</div>

@if(session('status'))
    <div class="alert success">{{ session('status') }}</div>
@endif
@if(session('error'))
    <div class="alert error">{{ session('error') }}</div>
@endif

<section class="tagihan-section">
    <div class="tagihan-intro">
        <div>
            <h2 class="section-title">Ringkasan Tagihan</h2>
            <p class="section-description">Tambahkan tagihan sekali atau rutin, lalu sistem akan memotong saldo secara otomatis sesuai frekuensi.</p>
        </div>
        <div class="tagihan-meta">
            <span>{{ $bills->count() }} Tagihan aktif</span>
            <span>{{ $bills->where('frequency', 'daily')->count() }} Harian</span>
            <span>{{ $bills->where('frequency', 'weekly')->count() }} Mingguan</span>
            <span>{{ $bills->where('frequency', 'monthly')->count() }} Bulanan</span>
            <span>{{ $bills->where('frequency', 'yearly')->count() }} Tahunan</span>
        </div>
    </div>

    @if($bills->isEmpty())
        <div class="empty-state-message">
            <p>Belum ada tagihan. Klik tombol Tambah Tagihan untuk mulai membuat daftar tagihan otomatis.</p>
        </div>
    @endif

    <div class="bill-grid">
        @foreach($bills as $bill)
            <article class="bill-card">
                <div class="bill-card-header">
                    <div>
                        <p class="bill-frequency">{{ ucfirst($bill->frequency) }}</p>
                        <h3>{{ $bill->name }}</h3>
                    </div>
                    <div class="bill-actions">
                        <button type="button" class="button-link edit-bill-button"
                            data-bill-id="{{ $bill->id }}"
                            data-period-type="{{ $bill->frequency }}"
                            data-name="{{ $bill->name }}"
                            data-amount="{{ $bill->plan_amount ?? $bill->amount }}"
                            data-start-date="{{ $bill->start_date ? $bill->start_date->toDateString() : now()->toDateString() }}"
                            data-duration-days="{{ $bill->duration_days }}"
                            data-monthly-option="{{ $bill->prorata ? 'prorata' : 'same_date' }}"
                            data-monthly-cycle-day="{{ $bill->monthly_cycle_day ?? ($bill->start_date ? $bill->start_date->day : now()->day) }}"
                            data-annual-discount="{{ $bill->annual_discount_pct }}">
                            Edit
                        </button>
                        <button type="button" class="button-danger delete-bill-button" data-bill-id="{{ $bill->id }}" data-bill-name="{{ $bill->name }}">
                            Hapus
                        </button>
                    </div>
                </div>
                <div class="bill-details">
                    <div>
                        <span class="label">Jumlah</span>
                        <strong>Rp {{ number_format($bill->amount, 0, ',', '.') }}</strong>
                    </div>
                    <div>
                        <span class="label">Jatuh Tempo</span>
                        <strong>{{ $bill->due_date->format('d M Y') }}</strong>
                    </div>
                    @if($bill->frequency === 'daily')
                        <div>
                            <span class="label">Durasi</span>
                            <strong>{{ $bill->duration_days ?? '-' }} hari</strong>
                        </div>
                    @endif
                    @if($bill->frequency === 'monthly')
                        <div>
                            <span class="label">Siklus</span>
                            <strong>{{ $bill->prorata ? 'Prorata' : 'Tanggal ' . ($bill->monthly_cycle_day ?? ($bill->start_date ? $bill->start_date->day : now()->day)) }}</strong>
                        </div>
                    @endif
                    @if($bill->frequency === 'yearly')
                        <div>
                            <span class="label">Diskon Tahunan</span>
                            <strong>{{ $bill->annual_discount_pct }}%</strong>
                        </div>
                    @endif
                    <div>
                        <span class="label">Status</span>
                        <strong>{{ ucfirst($bill->status) }}</strong>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<div class="modal-overlay" id="bill-modal" hidden>
    <div class="modal-card">
        <button type="button" class="modal-close" id="close-bill-modal" aria-label="Close modal">×</button>
        <h3 id="bill-modal-title">Tambah Tagihan Baru</h3>
        <p class="modal-text">Isi data tagihan untuk otomatis mengurangi saldo sesuai frekuensi yang Anda pilih.</p>

        <form id="bill-form" method="POST" action="{{ route('tagihan.add') }}">
            @csrf
            <input type="hidden" name="bill_id" id="modal-bill-id" value="">

            <label class="modal-label" for="modal-period-type">Periode Tagihan</label>
            <select id="modal-period-type" name="period_type" class="modal-input" required>
                <option value="daily">Harian</option>
                <option value="monthly">Bulanan</option>
                <option value="yearly">Tahunan</option>
            </select>

            <label class="modal-label" for="modal-bill-name">Nama Tagihan</label>
            <input id="modal-bill-name" name="name" type="text" class="modal-input" required>

            <label class="modal-label" for="modal-bill-amount">Harga (Rp)</label>
            <input id="modal-bill-amount" name="amount" type="text" inputmode="decimal" class="modal-input currency-input" required>

            <label class="modal-label" for="modal-start-date">Tanggal Mulai</label>
            <input id="modal-start-date" name="start_date" type="date" class="modal-input" value="{{ now()->toDateString() }}" required>

            <div class="period-group" id="daily-fields" hidden>
                <label class="modal-label" for="modal-duration-days">Jumlah Hari</label>
                <input id="modal-duration-days" name="duration_days" type="number" min="1" step="1" class="modal-input">
            </div>

            <div class="period-group" id="monthly-fields" hidden>
                <label class="modal-label" for="modal-monthly-option">Opsi Bulanan</label>
                <select id="modal-monthly-option" name="monthly_option" class="modal-input">
                    <option value="same_date">Tanggal yang sama setiap bulan</option>
                    <option value="prorata">Prorata awal bulan ini</option>
                </select>

                <label class="modal-label" for="modal-monthly-cycle-day">Tanggal Siklus</label>
                <input id="modal-monthly-cycle-day" name="monthly_cycle_day" type="number" min="1" max="28" step="1" class="modal-input" value="{{ now()->day }}">
            </div>

            <div class="period-group" id="yearly-fields" hidden>
                <label class="modal-label" for="modal-annual-discount">Diskon Tahunan (%)</label>
                <input id="modal-annual-discount" name="annual_discount_pct" type="number" min="0" max="100" step="0.01" class="modal-input" value="0">
            </div>

            <button type="submit" class="button-primary" id="modal-submit-button">Simpan Tagihan</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="delete-modal" hidden>
    <div class="modal-card">
        <button type="button" class="modal-close" id="close-delete-modal" aria-label="Close modal">×</button>
        <h3 style="margin: 0 0 12px; color: #DC2626; font-size: 1.3rem;">Konfirmasi Penghapusan</h3>
        <p style="margin: 0 0 24px; color: #6B7280;">Apakah Anda yakin ingin menghapus tagihan "<span id="delete-bill-name" style="font-weight: 600; color: #111827;"></span>"? Tindakan ini tidak dapat dibatalkan.</p>
        
        <div style="display: flex; gap: 12px; justify-content: flex-end;">
            <button type="button" class="button-secondary" id="cancel-delete-button">Batal</button>
            <button type="button" class="button-danger" id="confirm-delete-button">Hapus Tagihan</button>
        </div>
    </div>
</div>

<form id="delete-form" method="POST" action="{{ route('tagihan.delete') }}" style="display: none;">
    @csrf
    <input type="hidden" name="bill_id" id="delete-bill-id" value="">
</form>

<style>
    [hidden] {
        display: none !important;
    }

    .tagihan-section {
        display: grid;
        gap: 22px;
    }

    .tagihan-intro {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
    }

    .section-description {
        margin: 8px 0 0;
        color: #4B5563;
        max-width: 580px;
    }

    .tagihan-meta {
        display: inline-flex;
        flex-wrap: wrap;
        gap: 12px;
        color: #6B7280;
        font-size: 0.95rem;
    }

    .bill-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .bill-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 24px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
        padding: 22px;
        display: grid;
        gap: 18px;
    }

    .bill-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .bill-frequency {
        margin: 0 0 6px;
        color: #10B981;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.82rem;
    }

    .bill-card h3 {
        margin: 0;
        font-size: 1.1rem;
        color: #111827;
    }

    .bill-actions {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .bill-details {
        display: grid;
        gap: 16px;
    }

    .bill-details .label {
        display: block;
        margin-bottom: 6px;
        color: #6B7280;
        font-size: 0.88rem;
    }

    .bill-details strong {
        display: block;
        color: #111827;
        font-size: 1rem;
    }

    .alert {
        margin: 18px 0;
        padding: 14px 18px;
        border-radius: 14px;
        font-weight: 600;
    }

    .alert.success {
        background: #ECFDF5;
        color: #166534;
        border: 1px solid #D1FAE5;
    }

    .alert.error {
        background: #FEE2E2;
        color: #991B1B;
        border: 1px solid #FECACA;
    }

    .notification-summary {
        display: grid;
        gap: 10px;
        border: 1px solid #E5E7EB;
        border-radius: 20px;
        padding: 18px;
        background: #FFFFFF;
        box-shadow: 0 14px 40px rgba(15, 23, 42, 0.05);
        margin-bottom: 16px;
    }

    .notification-summary .notification-row {
        color: #475569;
        font-size: 0.95rem;
    }

    .period-group[hidden] {
        display: none;
    }

    .empty-state-message {
        padding: 28px;
        border-radius: 22px;
        background: #F8FAFC;
        border: 1px dashed #D1D5DB;
        color: #334155;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        display: grid;
        place-items: center;
        padding: 24px;
        z-index: 1000;
    }

    .modal-card {
        width: min(580px, 100%);
        background: #FFFFFF;
        border-radius: 28px;
        padding: 40px 44px;
        position: relative;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        max-height: 90vh;
        overflow-y: auto;
    }

    #bill-form {
        display: grid;
        gap: 24px;
    }

    .modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        border: none;
        background: transparent;
        font-size: 26px;
        cursor: pointer;
        color: #374151;
    }

    #bill-modal-title {
        margin: 0 0 8px;
        font-size: 1.4rem;
        color: #111827;
    }

    .modal-text {
        margin: 0 0 24px;
        color: #6B7280;
        font-size: 0.95rem;
    }

    .modal-label {
        margin: 0 0 10px;
        display: block;
        font-weight: 600;
        color: #374151;
    }

    .modal-input,
    .modal-input:focus {
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 16px;
        padding: 12px 16px;
        font-size: 0.95rem;
        color: #111827;
        outline: none;
        background: #FFFFFF;
    }

    .period-group {
        display: grid;
        gap: 18px;
    }

    #bill-form .button-primary {
        margin-top: 12px;
        padding: 14px 24px;
        font-size: 0.95rem;
    }

    .button-link {
        background: transparent;
        border: none;
        color: #1F9C8A;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
    }

    .button-primary {
        border: none;
        border-radius: 14px;
        background: #1F9C8A;
        color: #FFFFFF;
        padding: 12px 20px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .button-primary:hover {
        background-color: #1a8a78;
    }

    .button-danger {
        border: none;
        border-radius: 14px;
        background: #DC2626;
        color: #FFFFFF;
        padding: 12px 20px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .button-danger:hover {
        background-color: #b91c1c;
    }

    .button-secondary {
        border: 1px solid #D1D5DB;
        border-radius: 14px;
        background: #FFFFFF;
        color: #374151;
        padding: 12px 20px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
    }

    .button-secondary:hover {
        background-color: #F3F4F6;
        border-color: #9CA3AF;
    }

    @media (max-width: 960px) {
        .bill-grid {
            grid-template-columns: 1fr;
        }
    }

    .notification-trigger {
        position: relative;
        z-index: 9999;
    }
    .notification-badge {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 18px;
        height: 18px;
        border-radius: 999px;
        background: #EF4444;
        color: #FFFFFF;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }
    .notification-menu {
        position: absolute;
        top: 62px;
        right: 0;
        width: 320px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 18px;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.12);
        padding: 16px;
        z-index: 9998;
    }
    .notification-menu-header {
        border-bottom: 1px solid #E5E7EB;
        padding-bottom: 10px;
        margin-bottom: 12px;
        font-size: 0.95rem;
        color: #111827;
    }
    .notification-row {
        padding: 12px 0;
        border-bottom: 1px solid #F3F4F6;
        color: #334155;
        font-size: 0.9rem;
    }
    .notification-row:last-child {
        border-bottom: none;
    }
    .notification-row.empty {
        color: #9CA3AF;
        text-align: center;
    }
</style>

<script>
    const billModal = document.getElementById('bill-modal');
    const openBillModal = document.getElementById('open-bill-modal');
    const closeBillModal = document.getElementById('close-bill-modal');
    const billForm = document.getElementById('bill-form');
    const billModalTitle = document.getElementById('bill-modal-title');
    const modalBillId = document.getElementById('modal-bill-id');
    const modalPeriodType = document.getElementById('modal-period-type');
    const modalBillName = document.getElementById('modal-bill-name');
    const modalBillAmount = document.getElementById('modal-bill-amount');
    const modalStartDate = document.getElementById('modal-start-date');
    const modalDurationDays = document.getElementById('modal-duration-days');
    const modalMonthlyOption = document.getElementById('modal-monthly-option');
    const modalMonthlyCycleDay = document.getElementById('modal-monthly-cycle-day');
    const modalAnnualDiscount = document.getElementById('modal-annual-discount');
    const modalSubmitButton = document.getElementById('modal-submit-button');

    const dailyFields = document.getElementById('daily-fields');
    const monthlyFields = document.getElementById('monthly-fields');
    const yearlyFields = document.getElementById('yearly-fields');

    function updatePeriodFields(type) {
        dailyFields.hidden = type !== 'daily';
        monthlyFields.hidden = type !== 'monthly';
        yearlyFields.hidden = type !== 'yearly';
    }

    function openModal(mode, bill = null) {
        billModal.hidden = false;
        if (mode === 'edit') {
            billModalTitle.textContent = 'Edit Tagihan';
            billForm.action = '{{ route('tagihan.update') }}';
            modalBillId.value = bill.id;
            modalPeriodType.value = bill.periodType;
            modalBillName.value = bill.name;
            modalBillAmount.value = bill.amount;
            modalStartDate.value = bill.startDate;
            modalDurationDays.value = bill.durationDays || '';
            modalMonthlyOption.value = bill.monthlyOption || 'same_date';
            modalMonthlyCycleDay.value = bill.monthlyCycleDay || new Date().getDate();
            modalAnnualDiscount.value = bill.annualDiscount || 0;
            modalSubmitButton.textContent = 'Perbarui Tagihan';
            updatePeriodFields(bill.periodType);
        } else {
            billModalTitle.textContent = 'Tambah Tagihan Baru';
            billForm.action = '{{ route('tagihan.add') }}';
            modalBillId.value = '';
            modalPeriodType.value = 'monthly';
            modalBillName.value = '';
            modalBillAmount.value = '';
            modalStartDate.value = '{{ now()->toDateString() }}';
            modalDurationDays.value = '';
            modalMonthlyOption.value = 'same_date';
            modalMonthlyCycleDay.value = '{{ min(now()->day, 28) }}';
            modalAnnualDiscount.value = 0;
            modalSubmitButton.textContent = 'Simpan Tagihan';
            updatePeriodFields('monthly');
        }
    }

    modalPeriodType.addEventListener('change', function () {
        updatePeriodFields(this.value);
    });

    openBillModal.addEventListener('click', function () {
        openModal('add');
    });

    closeBillModal.addEventListener('click', function () {
        billModal.hidden = true;
    });

    document.querySelectorAll('.edit-bill-button').forEach(button => {
        button.addEventListener('click', function () {
            openModal('edit', {
                id: this.dataset.billId,
                periodType: this.dataset.periodType,
                name: this.dataset.name,
                amount: this.dataset.amount,
                startDate: this.dataset.startDate,
                durationDays: this.dataset.durationDays,
                monthlyOption: this.dataset.monthlyOption,
                monthlyCycleDay: this.dataset.monthlyCycleDay,
                annualDiscount: this.dataset.annualDiscount,
            });
        });
    });

    billModal.addEventListener('click', function (event) {
        if (event.target === billModal) {
            billModal.hidden = true;
        }
    });

    // Delete Confirmation Modal
    const deleteModal = document.getElementById('delete-modal');
    const closeDeleteModal = document.getElementById('close-delete-modal');
    const deleteForm = document.getElementById('delete-form');
    const deleteBillId = document.getElementById('delete-bill-id');
    const deleteBillName = document.getElementById('delete-bill-name');
    const confirmDeleteButton = document.getElementById('confirm-delete-button');
    const cancelDeleteButton = document.getElementById('cancel-delete-button');

    document.querySelectorAll('.delete-bill-button').forEach(button => {
        button.addEventListener('click', function () {
            deleteModal.hidden = false;
            deleteBillId.value = this.dataset.billId;
            deleteBillName.textContent = this.dataset.billName;
        });
    });

    closeDeleteModal.addEventListener('click', function () {
        deleteModal.hidden = true;
    });

    cancelDeleteButton.addEventListener('click', function () {
        deleteModal.hidden = true;
    });

    confirmDeleteButton.addEventListener('click', function () {
        deleteForm.submit();
    });

    deleteModal.addEventListener('click', function (event) {
        if (event.target === deleteModal) {
            deleteModal.hidden = true;
        }
    });

    // Notification Menu
    const notificationButton = document.getElementById('topbar-notification-button');
    const notificationMenu = document.getElementById('notification-menu');

    notificationButton.addEventListener('click', function () {
        notificationMenu.hidden = !notificationMenu.hidden;
    });

    document.addEventListener('click', function (event) {
        if (!notificationButton.contains(event.target) && !notificationMenu.contains(event.target)) {
            notificationMenu.hidden = true;
        }
    });
</script>

@endsection
