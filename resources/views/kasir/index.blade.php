<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="order-url" content="{{ route('kasir.order.store') }}">
    <title>Kasir - {{ $shopName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #1f2d27;
            --muted: #6c757d;
            --line: #e9ecef;
            --brand: #198754;
            --brand-dark: #146c43;
        }

        body {
            background: #f5f7f6;
            color: var(--ink);
        }

        .topbar {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid var(--line);
        }

        .pos-wrap {
            height: calc(100vh - 56px);
        }

        /* Kiri: menu */
        .menu-pane {
            flex: 1;
            min-width: 0;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: 14px;
        }

        .menu-grid {
            flex: 1;
            overflow-y: auto;
            padding: 2px 2px 90px;
        }

        @media (min-width: 992px) {
            .menu-grid {
                padding-bottom: 8px;
            }
        }

        .cat-scroll {
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 4px;
        }

        .cat-btn {
            border: 1px solid #cfd8d2;
            background: #fff;
            color: #495057;
            border-radius: 99px;
            padding: 5px 14px;
            font-size: .9rem;
            margin-right: 6px;
        }

        .cat-btn.on {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            font-weight: 500;
        }

        .menu-card {
            position: relative;
            cursor: pointer;
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 12px;
            overflow: hidden;
            height: 100%;
            transition: border-color .12s, box-shadow .12s;
        }

        .menu-card:hover {
            border-color: var(--brand);
            box-shadow: 0 2px 8px rgba(25, 135, 84, .15);
        }

        .menu-card:active {
            transform: scale(.98);
        }

        .menu-card.habis {
            opacity: .45;
            pointer-events: none;
        }

        .menu-img {
            display: block;
            width: 100%;
            aspect-ratio: 4 / 3;
            object-fit: cover;
            background: #eef0ed;
        }

        .menu-info {
            padding: 8px 10px 10px;
        }

        .menu-name {
            font-size: .9rem;
            font-weight: 600;
            line-height: 1.25;
        }

        .menu-price {
            color: var(--brand-dark);
            font-weight: 600;
            font-size: .9rem;
            margin-top: 2px;
        }

        .qty-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            min-width: 26px;
            height: 26px;
            padding: 0 7px;
            border-radius: 99px;
            background: var(--brand);
            color: #fff;
            font-size: .8rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .25);
        }

        /* Kanan: pesanan */
        .cart-pane {
            background: #fff;
        }

        @media (min-width: 992px) {
            .cart-pane {
                flex: 0 0 380px;
                border-left: 1px solid var(--line);
            }
        }

        @media (min-width: 1400px) {
            .cart-pane {
                flex-basis: 420px;
            }
        }

        .cart-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding: 0 16px;
        }

        .cart-item {
            padding: 12px 0;
            border-bottom: 1px solid var(--line);
        }

        .qty-ctl .btn {
            width: 30px;
            height: 30px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .cart-foot {
            background: #f8faf9;
            border-top: 1px solid var(--line);
            padding: 14px 16px;
        }

        .sum-row {
            display: flex;
            justify-content: space-between;
            font-size: .92rem;
            padding: 2px 0;
        }

        .sum-total {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 1.35rem;
            font-weight: 700;
            border-top: 1px solid #d5ddd8;
            margin-top: 6px;
            padding-top: 8px;
        }

        .btn-brand {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
            font-weight: 600;
        }

        .btn-brand:hover {
            background: var(--brand-dark);
            border-color: var(--brand-dark);
            color: #fff;
        }

        .btn-check:checked+.btn-outline-success {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .form-check-input:checked {
            background-color: var(--brand);
            border-color: var(--brand);
        }

        .btn,
        .form-control,
        .form-select {
            border-radius: 8px;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <nav class="topbar d-flex align-items-center justify-content-between px-3">
        <div class="d-flex align-items-center gap-2 overflow-hidden">
            <span class="d-inline-flex align-items-center justify-content-center rounded-3 text-white flex-shrink-0"
                style="width:34px;height:34px;background:var(--brand)"><i class="bi bi-shop"></i></span>
            <span class="fw-semibold text-truncate" style="max-width:42vw">{{ $shopName }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="small text-muted d-none d-sm-inline">
                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
            </span>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Panel admin</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger">Keluar</button>
            </form>
        </div>
    </nav>

    <div class="pos-wrap d-flex flex-column flex-lg-row">

        {{-- Kiri: katalog menu --}}
        <section class="menu-pane">
            <div class="position-relative mb-2">
                <i class="bi bi-search position-absolute top-50 translate-middle-y text-muted" style="left:12px"></i>
                <input type="search" id="search" class="form-control" style="padding-left:36px" placeholder="Cari menu">
            </div>
            <div class="cat-scroll mb-2" id="catTabs"></div>
            <div class="menu-grid">
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2" id="menuGrid"></div>
            </div>
        </section>

        {{-- Kanan: pesanan (di HP jadi panel dari bawah) --}}
        <aside class="offcanvas-lg offcanvas-bottom cart-pane d-flex flex-column" id="cartPanel" tabindex="-1" style="height:88vh">
            <div class="d-flex justify-content-between align-items-center px-3 py-3 border-bottom">
                <h2 class="h6 fw-semibold mb-0">Pesanan <span class="text-muted fw-normal" id="cartCount"></span></h2>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-danger" id="btnClear">Kosongkan</button>
                    <button class="btn-close d-lg-none" data-bs-dismiss="offcanvas" data-bs-target="#cartPanel" aria-label="Tutup"></button>
                </div>
            </div>

            <div class="cart-list" id="cartList"></div>

            <div class="cart-foot">
                <div class="btn-group w-100 mb-2">
                    <input type="radio" class="btn-check" name="order_type" id="tDine" value="dine_in" checked>
                    <label class="btn btn-outline-success" for="tDine">Makan di sini</label>
                    <input type="radio" class="btn-check" name="order_type" id="tTake" value="takeaway">
                    <label class="btn btn-outline-success" for="tTake">Bungkus</label>
                </div>
                <input type="text" id="tableNo" class="form-control mb-2" placeholder="Nomor meja (wajib)">
                <input type="text" id="orderNote" class="form-control mb-2" placeholder="Catatan, misalnya kuah dipisah">

                <div class="input-group mb-3">
                    <span class="input-group-text">Diskon</span>
                    <select id="discType" class="form-select" style="max-width:84px">
                        <option value="nominal">Rp</option>
                        <option value="percent">%</option>
                    </select>
                    <input type="number" id="discValue" min="0" class="form-control" placeholder="0">
                </div>

                <div class="sum-row"><span class="text-muted">Subtotal</span><span id="subtotal">Rp 0</span></div>
                <div class="sum-row text-danger d-none" id="rowDisc"><span>Diskon</span><span id="discAmt">- Rp 0</span></div>
                <div class="sum-row d-none" id="rowTax"><span class="text-muted" id="taxLabel">PPN</span><span id="taxAmt">Rp 0</span></div>
                <div class="sum-total"><span>Total</span><span id="grandTotal">Rp 0</span></div>

                <button class="btn btn-brand btn-lg w-100 mt-3" id="btnBayar">Bayar</button>
            </div>
        </aside>
    </div>

    {{-- Tombol keranjang melayang (HP) --}}
    <button class="btn btn-brand btn-lg shadow position-fixed bottom-0 start-0 end-0 m-3 d-lg-none"
        data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
        <i class="bi bi-cart3 me-1"></i>Pesanan (<span id="badgeQty">0</span>) &nbsp;|&nbsp; <span id="badgeTotal">Rp 0</span>
    </button>

    {{-- Jendela varian dan topping --}}
    <div class="modal fade" id="optModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6 fw-semibold" id="optTitle"></h2>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body" id="optBody"></div>
                <div class="modal-footer">
                    <button class="btn btn-brand w-100" id="optSave"></button>
                </div>
            </div>
        </div>
    </div>

    {{-- Jendela pembayaran --}}
    <div class="modal fade" id="payModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h2 class="modal-title h6 fw-semibold">Pembayaran</h2>
                    <button class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="text-muted small">Total bayar</div>
                        <div class="display-6 fw-bold text-success" id="payTotalText"></div>
                    </div>

                    <div class="btn-group w-100 mb-3">
                        <input type="radio" class="btn-check" name="pay_method" id="mTunai" value="tunai" checked>
                        <label class="btn btn-outline-success" for="mTunai"><i class="bi bi-cash-coin me-1"></i>Tunai</label>
                        <input type="radio" class="btn-check" name="pay_method" id="mQris" value="qris">
                        <label class="btn btn-outline-success" for="mQris"><i class="bi bi-qr-code me-1"></i>QRIS / transfer</label>
                    </div>

                    <div id="boxTunai">
                        <label class="form-label">Uang diterima</label>
                        <input type="number" id="cashIn" min="0" class="form-control form-control-lg mb-2" placeholder="0">
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-cash="pas">Uang pas</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-cash="20000">20rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-cash="50000">50rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-cash="100000">100rb</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline fs-5 p-2 rounded" style="background:#f1f5f3">
                            <span id="changeLabel">Kembalian</span>
                            <strong id="changeVal">Rp 0</strong>
                        </div>
                    </div>

                    <div id="boxQris" class="text-center d-none">
                        <img id="qrisImg" src="{{ $qrisUrl }}" class="img-fluid rounded border" style="max-height:340px" alt="QRIS">
                        <div class="text-muted small mt-2">Pastikan pembayaran sudah masuk sebelum menyimpan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-brand btn-lg w-100" id="btnSimpan"><i class="bi bi-printer me-1"></i>Simpan dan cetak</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Jendela sukses --}}
    <div class="modal fade" id="doneModal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
                <h2 class="h6 fw-semibold mt-2">Transaksi tersimpan</h2>
                <div class="text-muted small" id="doneInvoice"></div>
                <div class="mt-3" id="doneChangeBox">
                    <div class="text-muted small">Kembalian</div>
                    <div class="display-6 fw-bold" id="doneChange"></div>
                </div>
                <button class="btn btn-outline-secondary mt-3" id="btnReprint"><i class="bi bi-printer me-1"></i>Cetak ulang</button>
                <button class="btn btn-brand mt-2" data-bs-dismiss="modal">Pesanan baru</button>
            </div>
        </div>
    </div>

    <iframe id="printFrame" title="Cetak struk" style="position:absolute;width:0;height:0;border:0"></iframe>

    @php
    $posData = [
    'products' => $products,
    'categories' => $categories,
    'taxEnabled' => $taxEnabled,
    'taxRate' => $taxRate,
    ];
    @endphp
    <script type="application/json" id="pos-data">
        @json($posData)
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        var DATA = JSON.parse(document.getElementById('pos-data').textContent);
        var PRODUCTS = DATA.products;
        var CATS = DATA.categories;
        var TAX_ENABLED = DATA.taxEnabled;
        var TAX_RATE = DATA.taxRate;

        function $(id) {
            return document.getElementById(id);
        }

        function rp(n) {
            return 'Rp ' + Number(n).toLocaleString('id-ID');
        }

        function esc(s) {
            return String(s).replace(/[&<>"']/g, function(c) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                } [c];
            });
        }

        var cart = []; // {key, product, options, qty}
        var activeCat = 'all';
        var pending = null; // {p, editIndex}
        var payTotal = 0;
        var lastStrukUrl = '';

        var optModal = new bootstrap.Modal($('optModal'));
        var payModal = new bootstrap.Modal($('payModal'));
        var doneModal = new bootstrap.Modal($('doneModal'));

        /* ---------- Katalog ---------- */
        function renderTabs() {
            var tabs = [{
                id: 'all',
                name: 'Semua'
            }].concat(CATS);
            $('catTabs').innerHTML = tabs.map(function(c) {
                return '<button type="button" class="cat-btn ' + (String(c.id) === String(activeCat) ? 'on' : '') +
                    '" data-cat="' + c.id + '">' + esc(c.name) + '</button>';
            }).join('');
        }

        function renderMenu() {
            var q = $('search').value.trim().toLowerCase();
            var list = PRODUCTS.filter(function(p) {
                return (activeCat === 'all' || String(p.category_id) === String(activeCat)) &&
                    p.name.toLowerCase().indexOf(q) !== -1;
            });

            $('menuGrid').innerHTML = list.length ? list.map(function(p) {
                return '<div class="col"><div class="menu-card ' + (p.is_available ? '' : 'habis') + '" data-id="' + p.id + '">' +
                    '<span class="qty-badge d-none" data-pid="' + p.id + '"></span>' +
                    (p.image ? '<img src="' + p.image + '" class="menu-img" alt="">' : '<div class="menu-img"></div>') +
                    '<div class="menu-info"><div class="menu-name">' + esc(p.name) + '</div>' +
                    '<div class="menu-price">' + rp(p.price) + '</div>' +
                    (p.is_available ? '' : '<span class="badge text-bg-danger mt-1">Habis</span>') +
                    '</div></div></div>';
            }).join('') : '<div class="text-muted p-3">Menu tidak ditemukan.</div>';

            updateBadges();
        }

        function updateBadges() {
            document.querySelectorAll('.qty-badge').forEach(function(el) {
                var id = +el.dataset.pid;
                var n = cart.reduce(function(s, it) {
                    return s + (it.product.id === id ? it.qty : 0);
                }, 0);
                el.textContent = n;
                el.classList.toggle('d-none', n === 0);
            });
        }

        $('catTabs').addEventListener('click', function(e) {
            var b = e.target.closest('[data-cat]');
            if (!b) return;
            activeCat = b.dataset.cat;
            renderTabs();
            renderMenu();
        });
        $('menuGrid').addEventListener('click', function(e) {
            var c = e.target.closest('[data-id]');
            if (c) pick(+c.dataset.id);
        });
        $('search').addEventListener('input', renderMenu);

        /* ---------- Varian dan topping ---------- */
        function pick(id) {
            var p = PRODUCTS.find(function(x) {
                return x.id === id;
            });
            if (!p || !p.is_available) return;
            if (p.options.length === 0) {
                addToCart(p, []);
                return;
            }
            openModal(p, [], null);
        }

        function openModal(p, selected, editIndex) {
            pending = {
                p: p,
                editIndex: editIndex
            };
            var v = p.options.filter(function(o) {
                return o.type === 'varian';
            });
            var t = p.options.filter(function(o) {
                return o.type === 'topping';
            });
            var hasSel = v.some(function(o) {
                return selected.indexOf(o.id) !== -1;
            });
            var h = '';

            if (v.length) {
                h += '<div class="fw-semibold mb-1">Varian <span class="text-muted fw-normal small">(pilih satu)</span></div>';
                h += v.map(function(o, i) {
                    var on = hasSel ? selected.indexOf(o.id) !== -1 : i === 0;
                    return '<div class="form-check mb-1"><input class="form-check-input" type="radio" name="varian" id="o' + o.id +
                        '" value="' + o.id + '" ' + (on ? 'checked' : '') + '><label class="form-check-label" for="o' + o.id + '">' +
                        esc(o.name) + '</label></div>';
                }).join('');
            }
            if (t.length) {
                h += '<div class="fw-semibold mb-1 ' + (v.length ? 'mt-3' : '') + '">Topping <span class="text-muted fw-normal small">(boleh lebih dari satu)</span></div>';
                h += t.map(function(o) {
                    return '<div class="form-check mb-1"><input class="form-check-input" type="checkbox" id="o' + o.id + '" value="' + o.id + '" ' +
                        (selected.indexOf(o.id) !== -1 ? 'checked' : '') + '><label class="form-check-label" for="o' + o.id + '">' +
                        esc(o.name) + ' <small class="text-muted">+' + rp(o.price) + '</small></label></div>';
                }).join('');
            }

            $('optTitle').textContent = p.name;
            $('optBody').innerHTML = h;
            $('optSave').textContent = editIndex === null ? 'Tambah ke pesanan' : 'Simpan perubahan';
            optModal.show();
        }

        $('optSave').addEventListener('click', function() {
            var ids = Array.prototype.map.call($('optBody').querySelectorAll('input:checked'), function(i) {
                return +i.value;
            });
            var opts = pending.p.options.filter(function(o) {
                return ids.indexOf(o.id) !== -1;
            });
            if (pending.editIndex === null) addToCart(pending.p, opts);
            else updateItem(pending.editIndex, opts);
            optModal.hide();
        });

        /* ---------- Keranjang ---------- */
        function keyOf(p, opts) {
            return p.id + ':' + opts.map(function(o) {
                return o.id;
            }).sort(function(a, b) {
                return a - b;
            }).join(',');
        }

        function unitPrice(it) {
            return it.product.price + it.options.reduce(function(s, o) {
                return s + o.price;
            }, 0);
        }

        function addToCart(p, opts) {
            var key = keyOf(p, opts);
            var found = cart.find(function(c) {
                return c.key === key;
            });
            if (found) found.qty++;
            else cart.push({
                key: key,
                product: p,
                options: opts,
                qty: 1
            });
            renderCart();
        }

        function updateItem(i, opts) {
            var it = cart[i];
            it.options = opts;
            it.key = keyOf(it.product, opts);
            var dup = cart.findIndex(function(c, j) {
                return j !== i && c.key === it.key;
            });
            if (dup >= 0) {
                cart[dup].qty += it.qty;
                cart.splice(i, 1);
            }
            renderCart();
        }

        $('cartList').addEventListener('click', function(e) {
            var b = e.target.closest('[data-act]');
            if (!b) return;
            var i = +b.dataset.i;
            var act = b.dataset.act;
            if (act === 'plus') cart[i].qty++;
            else if (act === 'minus') {
                cart[i].qty--;
                if (cart[i].qty <= 0) cart.splice(i, 1);
            } else if (act === 'del') cart.splice(i, 1);
            else if (act === 'edit') {
                openModal(cart[i].product, cart[i].options.map(function(o) {
                    return o.id;
                }), i);
                return;
            }
            renderCart();
        });

        $('btnClear').addEventListener('click', function() {
            if (cart.length && confirm('Kosongkan semua pesanan?')) {
                cart = [];
                renderCart();
            }
        });

        function renderCart() {
            $('cartList').innerHTML = cart.length ? cart.map(function(it, i) {
                return '<div class="cart-item">' +
                    '<div class="d-flex justify-content-between gap-2"><div class="fw-semibold">' + esc(it.product.name) + '</div>' +
                    '<div class="fw-semibold text-nowrap">' + rp(unitPrice(it) * it.qty) + '</div></div>' +
                    (it.options.length ? '<div class="small text-muted">' + it.options.map(function(o) {
                        return esc(o.name);
                    }).join(', ') + '</div>' : '') +
                    '<div class="d-flex justify-content-between align-items-center mt-2">' +
                    '<div class="small text-muted">' + rp(unitPrice(it)) + ' / porsi</div>' +
                    '<div class="qty-ctl d-flex align-items-center gap-1">' +
                    '<button class="btn btn-outline-secondary" data-act="minus" data-i="' + i + '" aria-label="Kurangi"><i class="bi bi-dash"></i></button>' +
                    '<span class="px-2 fw-bold">' + it.qty + '</span>' +
                    '<button class="btn btn-outline-secondary" data-act="plus" data-i="' + i + '" aria-label="Tambah"><i class="bi bi-plus"></i></button>' +
                    (it.product.options.length ? '<button class="btn btn-outline-success ms-1" data-act="edit" data-i="' + i + '" aria-label="Ubah pilihan"><i class="bi bi-pencil"></i></button>' : '') +
                    '<button class="btn btn-outline-danger" data-act="del" data-i="' + i + '" aria-label="Hapus"><i class="bi bi-trash"></i></button>' +
                    '</div></div></div>';
            }).join('') : '<div class="text-center text-muted py-5"><i class="bi bi-basket fs-1 d-block mb-2"></i>Belum ada pesanan.<br>Pilih menu di sebelah kiri.</div>';

            updateTotals();
            updateBadges();
        }

        /* ---------- Total, diskon, PPN ---------- */
        function calc() {
            var sub = cart.reduce(function(s, it) {
                return s + unitPrice(it) * it.qty;
            }, 0);
            var type = $('discType').value;
            var val = Math.max(0, +$('discValue').value || 0);
            if (type === 'percent') val = Math.min(val, 100);

            var disc = type === 'percent' ? Math.round(sub * val / 100) : Math.round(val);
            disc = Math.min(disc, sub);

            var base = sub - disc;
            var tax = TAX_ENABLED ? Math.round(base * TAX_RATE / 100) : 0;
            return {
                sub: sub,
                disc: disc,
                tax: tax,
                total: base + tax
            };
        }

        function updateTotals() {
            var c = calc();
            var qty = cart.reduce(function(s, it) {
                return s + it.qty;
            }, 0);
            $('subtotal').textContent = rp(c.sub);
            $('rowDisc').classList.toggle('d-none', c.disc <= 0);
            $('discAmt').textContent = '- ' + rp(c.disc);
            $('rowTax').classList.toggle('d-none', !TAX_ENABLED);
            $('taxLabel').textContent = 'PPN ' + TAX_RATE + '%';
            $('taxAmt').textContent = rp(c.tax);
            $('grandTotal').textContent = rp(c.total);
            $('badgeTotal').textContent = rp(c.total);
            $('badgeQty').textContent = qty;
            $('cartCount').textContent = qty ? '(' + qty + ' porsi)' : '';
        }
        $('discType').addEventListener('change', updateTotals);
        $('discValue').addEventListener('input', updateTotals);

        /* ---------- Tipe pesanan ---------- */
        function orderType() {
            return document.querySelector('input[name=order_type]:checked').value;
        }

        function syncType() {
            $('tableNo').style.display = orderType() === 'dine_in' ? '' : 'none';
        }
        document.querySelectorAll('input[name=order_type]').forEach(function(r) {
            r.addEventListener('change', syncType);
        });

        /* ---------- Pembayaran ---------- */
        function payMethod() {
            return document.querySelector('input[name=pay_method]:checked').value;
        }

        $('btnBayar').addEventListener('click', function() {
            if (!cart.length) return alert('Pesanan masih kosong.');
            if (orderType() === 'dine_in' && !$('tableNo').value.trim()) return alert('Nomor meja wajib diisi untuk makan di sini.');

            payTotal = calc().total;
            $('payTotalText').textContent = rp(payTotal);
            $('cashIn').value = '';
            $('mTunai').checked = true;
            syncPay();

            var panel = bootstrap.Offcanvas.getInstance($('cartPanel'));
            if (panel) panel.hide();
            payModal.show();
        });

        function syncPay() {
            var tunai = payMethod() === 'tunai';
            $('boxTunai').classList.toggle('d-none', !tunai);
            $('boxQris').classList.toggle('d-none', tunai);

            var diff = (+$('cashIn').value || 0) - payTotal;
            $('changeLabel').textContent = diff < 0 ? 'Kurang' : 'Kembalian';
            $('changeVal').textContent = rp(Math.abs(diff));
            $('changeVal').className = diff < 0 ? 'text-danger' : 'text-success';
        }
        document.querySelectorAll('input[name=pay_method]').forEach(function(r) {
            r.addEventListener('change', syncPay);
        });
        $('cashIn').addEventListener('input', syncPay);

        document.querySelectorAll('[data-cash]').forEach(function(b) {
            b.addEventListener('click', function() {
                $('cashIn').value = b.dataset.cash === 'pas' ? payTotal : b.dataset.cash;
                syncPay();
            });
        });

        $('qrisImg').addEventListener('error', function() {
            this.outerHTML = '<div class="alert alert-warning">Gambar QRIS belum dipasang.<br>Unggah di menu Pengaturan.</div>';
        });

        $('btnSimpan').addEventListener('click', function() {
            var method = payMethod();
            var cash = +$('cashIn').value || 0;
            if (method === 'tunai' && cash < payTotal) return alert('Uang diterima masih kurang.');

            var btn = $('btnSimpan');
            btn.disabled = true;

            fetch(document.querySelector('meta[name=order-url]').content, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        order_type: orderType(),
                        table_number: $('tableNo').value.trim(),
                        note: $('orderNote').value.trim(),
                        payment_method: method,
                        cash_received: method === 'tunai' ? cash : null,
                        discount_type: $('discType').value,
                        discount_value: +$('discValue').value || 0,
                        items: cart.map(function(it) {
                            return {
                                product_id: it.product.id,
                                qty: it.qty,
                                option_ids: it.options.map(function(o) {
                                    return o.id;
                                })
                            };
                        })
                    })
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        if (!res.ok) throw new Error(data.message || 'Gagal menyimpan transaksi.');
                        return data;
                    });
                })
                .then(function(data) {
                    payModal.hide();
                    lastStrukUrl = data.struk_url;
                    $('doneInvoice').textContent = data.invoice_no + ' | ' + rp(data.total);
                    $('doneChangeBox').classList.toggle('d-none', data.change === null);
                    if (data.change !== null) $('doneChange').textContent = rp(data.change);
                    doneModal.show();

                    cart = [];
                    $('tableNo').value = '';
                    $('orderNote').value = '';
                    $('discValue').value = '';
                    $('discType').value = 'nominal';
                    renderCart();
                    reprint();
                })
                .catch(function(err) {
                    alert(err.message);
                })
                .then(function() {
                    btn.disabled = false;
                });
        });

        function reprint() {
            if (lastStrukUrl) $('printFrame').src = lastStrukUrl + '?print=1&t=' + Date.now();
        }
        $('btnReprint').addEventListener('click', reprint);

        /* ---------- Mulai ---------- */
        renderTabs();
        renderMenu();
        renderCart();
        syncType();
    </script>
</body>

</html>