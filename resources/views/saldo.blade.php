@extends('layout.app')

@section('title', 'Cash.Flow | Saldo')

@section('content')

<div class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Cash.Flow</h1>
        <div class="topbar-meta">
            <span>{{ now()->format('M d, Y') }}</span>
            <span class="meta-separator">•</span>
            <span>Manage your balances in one place</span>
        </div>
    </div>
    <div class="topbar-right">
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
        <div class="search-pill">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M11 19C15.4183 19 19 15.4183 19 11C19 6.58172 15.4183 3 11 3C6.58172 3 3 6.58172 3 11C3 15.4183 6.58172 19 11 19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M21 21L16.65 16.65" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
            <input id="saldo-search" type="text" placeholder="Telusuri" />
            <button type="button" class="search-button" aria-label="Search" id="search-button">Cari</button>
        </div>
        <div id="search-feedback" class="search-feedback" aria-live="polite"></div>
    </div>
</div>

@if(session('status'))
    <div class="alert success">{{ session('status') }}</div>
@endif
@if(session('error'))
    <div class="alert error">{{ session('error') }}</div>
@endif

@if(isset($bills))
    <section class="bill-summary-section">
        <div class="bill-summary-card">
            <div>
                <p class="balance-subtitle">Tagihan Aktif</p>
                <h3 class="balance-title">{{ $bills->count() }} Tagihan Terdaftar</h3>
            </div>
            <a href="{{ route('tagihan') }}" class="button-primary">Kelola Tagihan</a>
        </div>
        <div class="bill-summary-list">
            @forelse($bills as $bill)
                <article class="mini-bill-card">
                    <div>
                        <strong>{{ $bill->name }}</strong>
                        <p>{{ ucfirst($bill->frequency) }} · Jatuh tempo {{ $bill->due_date->format('d M Y') }}</p>
                    </div>
                    <span>Rp {{ number_format($bill->amount, 0, ',', '.') }}</span>
                </article>
            @empty
                <div class="empty-state-message">
                    <p>Belum ada tagihan. Tambahkan tagihan baru untuk melihat ringkasan di sini.</p>
                </div>
            @endforelse
        </div>
    </section>
@endif

<section class="balances-section">
    <h2 class="section-title">Balances</h2>

    @if($accounts->isEmpty())
        <div class="empty-state-message">
            <p>Belum ada akun terdaftar. Silakan buat akun baru untuk mulai mencatat saldo.</p>
        </div>
    @endif

    <div class="balance-grid">
        @foreach($accounts as $account)
            <article class="balance-card">
                <div class="balance-card-header">
                    <div class="balance-title-row">
                        <div>
                            <p class="balance-subtitle">{{ $account->type === 'cash' ? 'Uang Tunai' : 'Debit Card' }}</p>
                            <h3 class="balance-title">{{ $account->type === 'cash' ? 'Rupiah' : $account->name }}</h3>
                        </div>
                        <div class="balance-icon-wrapper">
                            <span class="balance-icon {{ $account->type === 'cash' ? 'cash-icon' : 'debit-icon' }}">{{ $account->type === 'cash' ? '💵' : '💳' }}</span>
                        </div>
                    </div>
                </div>

                <div class="balance-number">{{ $account->type === 'cash' ? 'Saldo Tunai' : ($account->account_number ?? 'No account number') }}</div>
                <p class="balance-info">{{ $account->type === 'cash' ? 'Cash account' : 'Account Number' }}</p>

                <div class="balance-total">Rp {{ number_format($account->balance, 0, ',', '.') }}</div>
                <p class="balance-label">Total amount</p>

                <div class="balance-actions">
                    <form method="POST" action="{{ route('saldo.account.remove') }}" class="inline-form">
                        @csrf
                        <input type="hidden" name="account_id" value="{{ $account->id }}">
                        <button class="button-link" type="submit">Remove</button>
                    </form>
                    <button type="button" class="button-primary open-account-modal"
                        data-mode="edit"
                        data-account-id="{{ $account->id }}"
                        data-type="{{ $account->type }}"
                        data-name="{{ $account->name }}"
                        data-account-number="{{ $account->account_number }}"
                        data-balance="{{ $account->balance }}">
                        Edit
                    </button>
                </div>
            </article>
        @endforeach

        <article class="balance-card add-account-card">
            <div class="add-card-content">
                <div>
                    <p class="balance-subtitle">Tambah Akun</p>
                    <h3 class="balance-title">Buat akun baru</h3>
                </div>
                <div class="add-icon">+</div>
            </div>
            <p class="balance-info">Tambahkan debit atau uang tunai</p>
            <button type="button" class="button-primary open-account-modal" data-mode="add">Create account</button>
        </article>
    </div>

    <div class="modal-overlay" id="account-modal" hidden>
        <div class="modal-card">
            <button type="button" class="modal-close" id="close-account-modal" aria-label="Close modal">×</button>
            <h3 id="account-modal-title">Edit Akun</h3>
            <p class="modal-text">Perbarui detail akun di sini untuk menyesuaikan saldo dan nomor akun.</p>

            <form id="account-form" method="POST" action="{{ route('saldo.account.update') }}">
                @csrf
                <input type="hidden" name="account_id" id="modal-account-id" value="">

                <label class="modal-label" for="modal-account-type">Tipe Akun</label>
                <select id="modal-account-type" name="type" class="modal-input">
                    <option value="debit">Debit Card</option>
                    <option value="cash">Uang Tunai</option>
                </select>

                <label class="modal-label" for="modal-account-name">Nama Akun</label>
                <input id="modal-account-name" name="name" type="text" class="modal-input" required>

                <label class="modal-label" for="modal-account-number">Nomor Akun</label>
                <input id="modal-account-number" name="account_number" type="text" class="modal-input" placeholder="Opsional">

                <label class="modal-label" for="modal-account-balance">Saldo</label>
                <input id="modal-account-balance" name="balance" type="text" inputmode="decimal" class="modal-input currency-input" required>

                <button type="submit" class="button-primary" id="modal-submit-button">Save</button>
            </form>
        </div>
    </div>
</section>

<style>
    .balances-section {
        margin-top: 24px;
        position: relative;
        z-index: 1;
    }

    .bill-summary-section {
        display: grid;
        gap: 18px;
        margin-top: 18px;
    }

    .bill-summary-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 24px;
        padding: 22px 24px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.06);
    }

    .bill-summary-list {
        display: grid;
        gap: 14px;
    }

    .mini-bill-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        padding: 18px 20px;
        border-radius: 20px;
        border: 1px solid #E5E7EB;
        background: #F9FAFB;
    }

    .mini-bill-card strong {
        display: block;
        font-size: 1rem;
        color: #111827;
    }

    .mini-bill-card p {
        margin: 6px 0 0;
        color: #6B7280;
        font-size: 0.9rem;
    }

    .button-primary {
        border: none;
        border-radius: 14px;
        background: #1F9C8A;
        color: #FFFFFF;
        padding: 12px 18px;
        font-weight: 700;
        cursor: pointer;
    }

    .section-title {
        margin: 0 0 18px;
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    .alert {
        margin: 16px 0 0;
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

    .empty-state-message {
        background: #F8FAFC;
        border: 1px solid #CBD5E1;
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 20px;
        color: #334155;
        font-weight: 600;
    }

    .balance-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .balance-card {
        background: #FFFFFF;
        border: 1px solid #E5E7EB;
        border-radius: 24px;
        box-shadow: 0 18px 48px rgba(15, 23, 42, 0.06);
        padding: 24px;
        display: grid;
        gap: 20px;
    }

    .balance-card-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
    }

    .balance-subtitle {
        margin: 0;
        font-size: 0.9rem;
        color: #6B7280;
    }

    .balance-title {
        margin: 6px 0 0;
        font-size: 1rem;
        font-weight: 700;
        color: #111827;
    }

    .brand-badge,
    .cash-icon,
    .debit-icon,
    .balance-icon {
        width: 48px;
        height: 48px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .brand-badge,
    .debit-icon {
        background: linear-gradient(90deg, #F59E0B 0%, #EF4444 100%);
    }

    .cash-icon {
        background: linear-gradient(90deg, #34D399 0%, #10B981 100%);
    }

    .balance-icon-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        background: #F8FAFC;
    }

    .balance-title-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .balance-number {
        font-size: 1.2rem;
        font-weight: 700;
        color: #111827;
        letter-spacing: 0.08em;
    }

    .balance-info,
    .balance-label {
        margin: 0;
        color: #6B7280;
        font-size: 0.9rem;
    }

    .balance-total {
        margin: 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #111827;
    }

    .balance-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .balance-input-label {
        display: block;
        margin: 0 0 6px;
        color: #374151;
        font-size: 0.92rem;
        font-weight: 600;
    }

    .balance-input {
        width: 100%;
        padding: 12px 14px;
        border: 1px solid #D1D5DB;
        border-radius: 14px;
        font-size: 0.95rem;
        color: #111827;
        margin-bottom: 14px;
    }

    .account-details {
        background: #F9FAFB;
        border-radius: 18px;
        padding: 18px;
        border: 1px solid #E5E7EB;
    }

    .inline-form {
        margin: 0;
    }

    .add-account-card {
        border: 2px dashed #D1D5DB;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: flex-start;
        min-height: 240px;
        padding: 24px;
    }

    .add-card-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }

    .add-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 1.5rem;
        background: #E5E7EB;
        color: #111827;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        display: none;
        place-items: center;
        padding: 24px;
        z-index: 50;
    }

    .modal-overlay:not([hidden]) {
        display: grid;
    }

    .modal-card {
        background: #FFFFFF;
        border-radius: 24px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        width: min(520px, 100%);
        padding: 28px;
        position: relative;
    }

    .modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        border: none;
        background: transparent;
        font-size: 1.5rem;
        cursor: pointer;
    }

    .modal-text {
        margin: 8px 0 24px;
        color: #4B5563;
        line-height: 1.6;
    }

    .modal-label {
        display: block;
        margin: 0 0 8px;
        color: #374151;
        font-weight: 600;
    }

    .modal-input {
        width: 100%;
        padding: 14px 16px;
        border: 1px solid #D1D5DB;
        border-radius: 16px;
        font-size: 0.95rem;
        margin-bottom: 18px;
    }

    #modal-submit-button {
        width: 100%;
    }

    .button-link {
        background: transparent;
        border: none;
        color: #10B981;
        font-weight: 700;
        cursor: pointer;
        padding: 0;
    }

    .button-primary {
        border: none;
        border-radius: 14px;
        background: #1F9C8A;
        color: #FFFFFF;
        padding: 10px 18px;
        font-weight: 700;
        cursor: pointer;
    }

    @media (max-width: 980px) {
        .balance-grid {
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

    .search-feedback {
        margin-top: 8px;
        padding: 0 12px;
        color: #EF4444;
        font-size: 0.9rem;
    }
</style>

<script>
    const accountModal = document.getElementById('account-modal');
    const accountForm = document.getElementById('account-form');
    const accountTitle = document.getElementById('account-modal-title');
    const modalAccountId = document.getElementById('modal-account-id');
    const modalAccountType = document.getElementById('modal-account-type');
    const modalAccountName = document.getElementById('modal-account-name');
    const modalAccountNumber = document.getElementById('modal-account-number');
    const modalAccountBalance = document.getElementById('modal-account-balance');
    const modalSubmitButton = document.getElementById('modal-submit-button');
    const closeAccountModal = document.getElementById('close-account-modal');

    const addAction = '{{ route('saldo.account.add') }}';
    const updateAction = '{{ route('saldo.account.update') }}';

    function openModal(mode, account = null) {
        accountModal.hidden = false;
        if (mode === 'add') {
            accountTitle.textContent = 'Tambah Akun Baru';
            accountForm.action = addAction;
            modalAccountId.value = '';
            modalAccountType.value = 'debit';
            modalAccountName.value = '';
            modalAccountNumber.value = '';
            modalAccountBalance.value = '0';
            modalSubmitButton.textContent = 'Tambah Akun';
        } else {
            accountTitle.textContent = 'Edit Akun';
            accountForm.action = updateAction;
            modalAccountId.value = account.id;
            modalAccountType.value = account.type;
            modalAccountName.value = account.name;
            modalAccountNumber.value = account.account_number || '';
            modalAccountBalance.value = account.balance;
            modalSubmitButton.textContent = 'Simpan Perubahan';
        }
    }

    function closeModal() {
        accountModal.hidden = true;
    }

    document.querySelectorAll('.open-account-modal').forEach(button => {
        button.addEventListener('click', () => {
            const mode = button.dataset.mode;
            if (mode === 'add') {
                openModal('add');
                return;
            }

            const account = {
                id: button.dataset.accountId,
                type: button.dataset.type,
                name: button.dataset.name,
                account_number: button.dataset.accountNumber,
                balance: button.dataset.balance,
            };
            openModal('edit', account);
        });
    });

    closeAccountModal.addEventListener('click', closeModal);
    accountModal.addEventListener('click', (event) => {
        if (event.target === accountModal) {
            closeModal();
        }
    });

    // Notification and Search
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

    const searchInput = document.getElementById('saldo-search');
    const searchButton = document.getElementById('search-button');
    const transactionItems = document.querySelectorAll('.account-item');

    const searchFeedback = document.getElementById('search-feedback');

    function performSearch() {
        const term = searchInput.value.trim().toLowerCase();
        let found = false;

        transactionItems.forEach(item => {
            const text = item.textContent.toLowerCase();
            const match = term === '' || text.includes(term);
            item.style.display = match ? 'grid' : 'none';
            if (match) found = true;
        });

        const totalItems = transactionItems.length;
        if (term !== '' && !found && totalItems > 0) {
            searchFeedback.textContent = 'Tidak ada hasil untuk pencarian ini.';
        } else {
            searchFeedback.textContent = '';
        }
    }

    searchButton.addEventListener('click', performSearch);
    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            performSearch();
        }
    });
</script>

@endsection
