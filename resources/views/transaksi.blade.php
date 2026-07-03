@extends('layout.app')

@section('title', 'Cash.Flow | Transaksi')

@section('content')

<div class="topbar">
    <div class="topbar-left">
        <h1 class="topbar-title">Transaksi</h1>
        <div class="topbar-meta">
            <span>{{ now()->format('M d, Y') }}</span>
            <span class="meta-separator">•</span>
            <span>Kelola pemasukan dan pengeluaran harianmu.</span>
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
            <input id="transaction-search" type="text" placeholder="Telusuri" autocomplete="off" />
            <button type="button" class="search-button" aria-label="Search" id="search-button">Cari</button>
        </div>
        <div id="search-feedback" class="search-feedback" aria-live="polite"></div>
        <button type="button" class="button-primary" id="open-transaction-modal">Tambah Transaksi</button>
    </div>
</div>

<div class="grid-2" style="margin-bottom: 24px; gap: 18px;">
    <div class="card">
        <div class="card-small">
            <div>
                <h3>Riwayat Pendapatan</h3>
                <strong>Rp {{ number_format($incomeTotal, 0, ',', '.') }}</strong>
            </div>
            <a href="{{ url('/transaksi') }}" class="card-badge">Lihat Semua</a>
        </div>

        <div class="transaction-list">
            @forelse($transactions->where('type', 'income')->take(6) as $trx)
                <div class="transaction-item" data-type="income" data-description="{{ strtolower($trx->description . ' ' . $trx->category->name) }}">
                    <div class="transaction-icon">{{ $trx->category->icon ?? '💰' }}</div>
                    <div class="transaction-details">
                        <strong>{{ $trx->description }}</strong>
                        <span>{{ $trx->category->name ?? 'Pendapatan' }} · {{ $trx->date->format('d M Y') }}</span>
                    </div>
                    <div class="transaction-price">
                        <strong>+Rp {{ number_format($trx->amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada pemasukan. Tambahkan transaksi baru untuk mulai mencatat.</p>
            @endforelse
        </div>
    </div>

    <div class="card">
        <div class="card-small">
            <div>
                <h3>Riwayat Pengeluaran</h3>
                <strong>Rp {{ number_format($expenseTotal, 0, ',', '.') }}</strong>
            </div>
            <a href="{{ url('/transaksi') }}" class="card-badge">Lihat Semua</a>
        </div>

        <div class="transaction-list">
            @forelse($transactions->where('type', 'expense')->take(6) as $trx)
                <div class="transaction-item" data-type="expense" data-description="{{ strtolower($trx->description . ' ' . $trx->category->name) }}">
                    <div class="transaction-icon">{{ $trx->category->icon ?? '💸' }}</div>
                    <div class="transaction-details">
                        <strong>{{ $trx->description }}</strong>
                        <span>{{ $trx->category->name ?? 'Pengeluaran' }} · {{ $trx->date->format('d M Y') }}</span>
                    </div>
                    <div class="transaction-price">
                        <strong>-Rp {{ number_format($trx->amount, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @empty
                <p class="empty-state">Belum ada pengeluaran. Tambahkan transaksi baru untuk mulai mencatat.</p>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-small" style="align-items: center;">
        <div>
            <h3>Daftar Transaksi</h3>
            <span class="meta-subtitle">Semua aktivitas terakhir, bisa diedit kapan saja.</span>
        </div>
        <div class="history-tabs" id="transaction-tabs">
            <button class="history-tab active" data-filter="all">Semua</button>
            <button class="history-tab" data-filter="income">Pendapatan</button>
            <button class="history-tab" data-filter="expense">Pengeluaran</button>
        </div>
    </div>

    <div class="transaction-list" id="transaction-list">
        @forelse($transactions as $trx)
            <div class="transaction-item" data-type="{{ $trx->type }}" data-description="{{ strtolower($trx->description . ' ' . $trx->category->name) }}">
                <div class="transaction-icon">{{ $trx->category->icon ?? ($trx->type === 'income' ? '💰' : '💸') }}</div>
                <div class="transaction-details">
                    <strong>{{ $trx->description }}</strong>
                    <span>{{ $trx->category->name ?? 'Transaksi' }} · {{ $trx->date->format('d M Y') }}</span>
                </div>
                <div class="transaction-price">
                    <strong>{{ $trx->type === 'income' ? '+' : '-' }}Rp {{ number_format($trx->amount, 0, ',', '.') }}</strong>
                    <button type="button" class="edit-transaction-btn" data-id="{{ $trx->id }}" data-account-id="{{ $trx->account_id }}" data-category-id="{{ $trx->category_id }}" data-type="{{ $trx->type }}" data-amount="{{ $trx->amount }}" data-description="{{ $trx->description }}" data-date="{{ $trx->date->format('Y-m-d') }}">Edit</button>
                    <form method="POST" action="{{ route('transaksi.delete') }}" class="transaction-delete-form" onsubmit="return confirm('Hapus transaksi ini? Saldo akun akan disesuaikan kembali.');">
                        @csrf
                        <input type="hidden" name="transaction_id" value="{{ $trx->id }}" />
                        <button type="submit" class="delete-transaction-btn" aria-label="Hapus transaksi">−</button>
                    </form>
                </div>
            </div>
        @empty
            <p class="empty-state no-transaction-message">Belum ada transaksi. Tambahkan transaksi baru untuk memulai pencatatan.</p>
        @endforelse
    </div>
</div>

<div class="modal-overlay" id="transaction-modal" aria-hidden="true">
    <div class="modal-card">
        <button type="button" class="modal-close" id="close-transaction-modal" aria-label="Close modal">×</button>
        <h3 id="modal-title">Tambah Transaksi</h3>
        <form id="transaction-form" method="POST" action="{{ route('transaksi.add') }}">
            @csrf
            <input type="hidden" name="transaction_id" id="transaction-id" value="" />

            <div class="form-grid">
                <label class="modal-label" for="transaction-type">Jenis</label>
                <select id="transaction-type" name="type" class="modal-input" required>
                    <option value="income">Pendapatan</option>
                    <option value="expense">Pengeluaran</option>
                </select>

                <label class="modal-label" for="transaction-account">Akun</label>
                <select id="transaction-account" name="account_id" class="modal-input" required>
                    @foreach($accounts as $account)
                        <option value="{{ $account->id }}">{{ $account->type === 'cash' ? 'Uang Tunai' : $account->name }} - {{ $account->type === 'cash' ? 'Rp ' . number_format($account->balance, 0, ',', '.') : $account->account_number ?? 'No Number' }}</option>
                    @endforeach
                </select>

                <label class="modal-label" for="transaction-category">Kategori</label>
                <select id="transaction-category" name="category_id" class="modal-input" required>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" data-type="{{ $category->type }}">{{ $category->icon }} {{ $category->name }}</option>
                    @endforeach
                </select>

                <label class="modal-label" for="transaction-date">Tanggal</label>
                <input id="transaction-date" type="date" name="date" class="modal-input" value="{{ now()->format('Y-m-d') }}" required />
            </div>

            <label class="modal-label" for="transaction-description">Deskripsi</label>
            <input id="transaction-description" type="text" name="description" class="modal-input" placeholder="Contoh: Beli game" required />

            <label class="modal-label" for="transaction-amount">Jumlah</label>
            <input id="transaction-amount" type="text" inputmode="decimal" name="amount" class="modal-input currency-input" placeholder="Rp 0,00" required />

            <button type="submit" class="button-primary" id="save-transaction-btn">Simpan Transaksi</button>
        </form>
    </div>
</div>

<script>
    const transactionModal = document.getElementById('transaction-modal');
    const openTransactionModal = document.getElementById('open-transaction-modal');
    const closeTransactionModal = document.getElementById('close-transaction-modal');
    const modalTitle = document.getElementById('modal-title');
    const transactionForm = document.getElementById('transaction-form');
    const transactionIdInput = document.getElementById('transaction-id');
    const transactionType = document.getElementById('transaction-type');
    const transactionCategory = document.getElementById('transaction-category');
    const transactionAccount = document.getElementById('transaction-account');
    const transactionDescription = document.getElementById('transaction-description');
    const transactionAmount = document.getElementById('transaction-amount');
    const transactionDate = document.getElementById('transaction-date');
    const transactionTabs = document.querySelectorAll('#transaction-tabs .history-tab');
    const transactionItems = document.querySelectorAll('#transaction-list .transaction-item');
    const transactionSearch = document.getElementById('transaction-search');

    function toggleModal(open) {
        transactionModal.setAttribute('aria-hidden', open ? 'false' : 'true');
        transactionModal.style.display = open ? 'flex' : 'none';
    }

    function filterCategoryOptions() {
        const selectedType = transactionType.value;
        Array.from(transactionCategory.options).forEach(option => {
            option.hidden = option.dataset.type !== selectedType;
            option.disabled = option.dataset.type !== selectedType;
        });
        const visibleOption = Array.from(transactionCategory.options).find(opt => !opt.hidden);
        if (visibleOption) {
            transactionCategory.value = visibleOption.value;
        }
    }

    function resetModal() {
        modalTitle.textContent = 'Tambah Transaksi';
        transactionForm.action = '{{ route('transaksi.add') }}';
        transactionIdInput.value = '';
        transactionType.value = 'expense';
        transactionDescription.value = '';
        transactionAmount.value = '';
        transactionDate.value = '{{ now()->format('Y-m-d') }}';
        filterCategoryOptions();
    }

    openTransactionModal.addEventListener('click', () => {
        resetModal();
        toggleModal(true);
    });

    closeTransactionModal.addEventListener('click', () => {
        toggleModal(false);
    });

    transactionType.addEventListener('change', filterCategoryOptions);
    filterCategoryOptions();

    document.querySelectorAll('.edit-transaction-btn').forEach(button => {
        button.addEventListener('click', function () {
            const id = this.dataset.id;
            modalTitle.textContent = 'Edit Transaksi';
            transactionForm.action = '{{ route('transaksi.update') }}';
            transactionIdInput.value = id;
            transactionType.value = this.dataset.type;
            transactionDescription.value = this.dataset.description;
            transactionAmount.value = this.dataset.amount;
            transactionDate.value = this.dataset.date;
            transactionAccount.value = this.dataset.accountId;
            filterCategoryOptions();
            transactionCategory.value = this.dataset.categoryId;
            toggleModal(true);
        });
    });

    function applyFilter() {
        const filterType = document.querySelector('#transaction-tabs .history-tab.active').dataset.filter;
        const searchTerm = transactionSearch.value.trim().toLowerCase();
        let visibleCount = 0;

        transactionItems.forEach(item => {
            const itemType = item.dataset.type;
            const text = item.dataset.description;
            const matchesType = filterType === 'all' || itemType === filterType;
            const matchesSearch = searchTerm === '' || text.includes(searchTerm);
            const visible = matchesType && matchesSearch;
            item.style.display = visible ? 'flex' : 'none';
            if (visible) visibleCount += 1;
        });

        const emptyNotice = document.querySelector('.no-transaction-message');
        if (!emptyNotice) {
            const message = document.createElement('p');
            message.className = 'empty-state no-transaction-message';
            message.textContent = 'Tidak ada transaksi yang cocok.';
            document.getElementById('transaction-list').appendChild(message);
        }
        document.querySelectorAll('.no-transaction-message').forEach(el => {
            el.style.display = visibleCount === 0 ? 'block' : 'none';
        });
    }

    transactionTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            transactionTabs.forEach(btn => btn.classList.remove('active'));
            tab.classList.add('active');
            applyFilter();
        });
    });

    transactionSearch.addEventListener('input', applyFilter);
    applyFilter();

    // Notification and Search
    const notificationButton = document.getElementById('topbar-notification-button');
    const notificationMenu = document.getElementById('notification-menu');
    const searchButton = document.getElementById('search-button');
    const searchFeedback = document.getElementById('search-feedback');

    notificationButton.addEventListener('click', function () {
        notificationMenu.hidden = !notificationMenu.hidden;
    });

    document.addEventListener('click', function (event) {
        if (!notificationButton.contains(event.target) && !notificationMenu.contains(event.target)) {
            notificationMenu.hidden = true;
        }
    });

    searchButton.addEventListener('click', function () {
        applyFilter();
    });
</script>

<style>
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }
    .transaction-list {
        display: grid;
        gap: 12px;
    }
    .transaction-item {
        display: grid;
        gap: 10px;
    }
    .transaction-price {
        text-align: right;
        min-width: 120px;
        display: grid;
        gap: 8px;
        align-items: flex-end;
    }
    .transaction-price button {
        border: none;
        background: #E5E7EB;
        color: #111827;
        padding: 8px 12px;
        border-radius: 14px;
        cursor: pointer;
        font-size: 0.85rem;
    }
    .transaction-price button:hover {
        background: #D1D5DB;
    }
    .transaction-delete-form {
        margin: 0;
        justify-self: end;
    }
    .delete-transaction-btn {
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 999px;
        background: #EF4444;
        color: #FFFFFF;
        cursor: pointer;
        font-size: 1.05rem;
        line-height: 1;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 10px 18px rgba(239, 68, 68, 0.22);
    }
    .delete-transaction-btn:hover {
        background: #DC2626;
    }
    .meta-subtitle {
        color: #6B7280;
        font-size: 0.95rem;
    }
    .empty-state {
        padding: 18px;
        border-radius: 18px;
        background: #F8FAFC;
        border: 1px solid #E5E7EB;
        color: #6B7280;
        margin: 0;
    }
    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 16px;
    }
    .modal-label {
        display: block;
        margin-bottom: 6px;
        font-weight: 600;
        color: #111827;
    }
    .modal-input {
        width: 100%;
        border: 1px solid #E5E7EB;
        border-radius: 14px;
        padding: 12px 14px;
        font-size: 0.95rem;
        background: #FFFFFF;
        outline: none;
        box-sizing: border-box;
    }
    .button-primary {
        border: none;
        background: #1F9C8A;
        color: #FFFFFF;
        padding: 12px 18px;
        border-radius: 16px;
        cursor: pointer;
        font-weight: 700;
    }
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.35);
        align-items: center;
        justify-content: center;
        z-index: 999;
        padding: 20px;
    }
    .modal-card {
        width: min(680px, 100%);
        background: #FFFFFF;
        border-radius: 28px;
        padding: 28px;
        position: relative;
        box-shadow: 0 20px 60px rgba(15, 23, 42, 0.18);
    }
    .modal-close {
        position: absolute;
        top: 18px;
        right: 18px;
        border: none;
        background: transparent;
        font-size: 1.4rem;
        line-height: 1;
        cursor: pointer;
        color: #6B7280;
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

@endsection
