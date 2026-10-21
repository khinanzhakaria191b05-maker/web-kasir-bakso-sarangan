<!DOCTYPE html>
<html lang="id">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kasir - {{ $shopName }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background: #f1f3f5;
        }

        .pos-wrap {
            height: calc(100vh - 56px);
        }

        .menu-pane {
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            padding: 12px;
        }

        .menu-grid {
            flex: 1;
            overflow-y: auto;
            padding-bottom: 80px;
        }

        .menu-card {
            cursor: pointer;
            transition: transform .1s;
        }

        .menu-card:active {
            transform: scale(.97);
        }

        .menu-card.habis {
            opacity: .4;
            pointer-events: none;
        }

        .menu-img {
            height: 100px;
            width: 100%;
            object-fit: cover;
        }

        .cat-scroll {
            overflow-x: auto;
            white-space: nowrap;
        }

        .cart-list {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
        }

        @media (min-width: 992px) {
            .menu-pane {
                flex: 0 0 65%;
            }

            .menu-grid {
                padding-bottom: 0;
            }

            .cart-pane {
                flex: 0 0 35% !important;
                background: #fff !important;
                border-left: 1px solid #dee2e6 !important;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar bg-white border-bottom" style="height:56px">
        <div class="container-fluid">
            <span class="navbar-brand fw-bold text-success mb-0 text-truncate" style="max-width:40vw">{{ $shopName }}</span>
            <div class="d-flex align-items-center gap-2">
                <span class="small text-muted d-none d-sm-inline">
                    <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})
                </span>
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">Panel Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-danger">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="pos-wrap d-flex flex-column flex-lg-row">

        {{-- KIRI: katalog menu --}}
        <section class="menu-pane">
            <input type="search" id="search" class="form-control mb-2" placeholder="Cari menu...">
            <div class="cat-scroll mb-2 pb-1" id="catTabs"></div>
            <div class="menu-grid">
                <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-2" id="menuGrid"></div>
            </div>
        </section>

        {{-- KANAN: keranjang (di HP jadi panel tarik dari bawah) --}}
        <aside class="offcanvas-lg offcanvas-bottom cart-pane d-flex flex-column" id="cartPanel" tabindex="-1" style="height:85vh">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="mb-0"><i class="bi bi-receipt me-1"></i>Pesanan</h5>
                <div>
                    <button class="btn btn-sm btn-outline-danger" onclick="clearCart()">Kosongkan</button>
                    <button class="btn-close d-lg-none ms-2" data-bs-dismiss="offcanvas" data-bs-target="#cartPanel"></button>
                </div>
            </div>

            <div class="cart-list px-3" id="cartList"></div>

            <div class="p-3 border-top">
                <div class="btn-group w-100 mb-2">
                    <input type="radio" class="btn-check" name="order_type" id="tDine" value="dine_in" checked>
                    <label class="btn btn-outline-success" for="tDine">Makan di Sini</label>
                    <input type="radio" class="btn-check" name="order_type" id="tTake" value="takeaway">
                    <label class="btn btn-outline-success" for="tTake">Bungkus</label>
                </div>
                <input type="text" id="tableNo" class="form-control mb-2" placeholder="Nomor meja (wajib)">
                <textarea id="orderNote" class="form-control mb-2" rows="2" placeholder="Catatan khusus (mis. kuah dipisah)"></textarea>

                <div class="d-flex justify-content-between fs-5 fw-bold mb-2">
                    <span>Subtotal</span><span id="subtotal">Rp 0</span>
                </div>
                <button class="btn btn-success btn-lg w-100 fw-bold" onclick="bayar()">BAYAR</button>
            </div>
        </aside>
    </div>

    {{-- Tombol keranjang melayang (HP) --}}
    <button class="btn btn-success btn-lg shadow position-fixed bottom-0 start-0 end-0 m-3 d-lg-none"
        data-bs-toggle="offcanvas" data-bs-target="#cartPanel">
        <i class="bi bi-cart3 me-1"></i> Keranjang (<span id="badgeQty">0</span>) · <span id="badgeTotal">Rp 0</span>
    </button>

    {{-- Pop-up varian & topping --}}
    <div class="modal fade" id="optModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="optTitle"></h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="optBody"></div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100" id="optSave"></button>
                </div>
            </div>
        </div>
    </div>
    {{-- Pop-up pembayaran --}}
    <div class="modal fade" id="payModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pembayaran</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <div class="text-muted">Total Bayar</div>
                        <div class="display-6 fw-bold text-success" id="payTotal"></div>
                    </div>

                    <div class="btn-group w-100 mb-3">
                        <input type="radio" class="btn-check" name="pay_method" id="mTunai" value="tunai" checked>
                        <label class="btn btn-outline-success" for="mTunai"><i class="bi bi-cash-coin me-1"></i>Tunai</label>
                        <input type="radio" class="btn-check" name="pay_method" id="mQris" value="qris">
                        <label class="btn btn-outline-success" for="mQris"><i class="bi bi-qr-code me-1"></i>QRIS / Transfer</label>
                    </div>

                    <div id="boxTunai">
                        <label class="form-label">Uang Diterima</label>
                        <input type="number" id="cashIn" min="0" class="form-control form-control-lg mb-2" placeholder="0">
                        <div class="d-flex flex-wrap gap-1 mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCash(payTotal)">Uang Pas</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCash(20000)">20rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCash(50000)">50rb</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="setCash(100000)">100rb</button>
                        </div>
                        <div class="d-flex justify-content-between fs-5">
                            <span id="changeLabel">Kembalian</span>
                            <strong id="changeVal">Rp 0</strong>
                        </div>
                    </div>

                    <div id="boxQris" class="text-center d-none">
                        <img src="{{ asset('images/qris.png') }}" class="img-fluid rounded border" style="max-height:340px"
                            onerror="this.outerHTML='<div class=&quot;alert alert-warning&quot;>Gambar QRIS belum dipasang.<br>Taruh file di <code>public/images/qris.png</code></div>'">
                        <div class="text-muted small mt-2">Pastikan pembayaran sudah masuk sebelum menyimpan.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success btn-lg w-100" id="btnSimpan"><i class="bi bi-printer me-1"></i>Simpan & Cetak</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Pop-up sukses --}}
    <div class="modal fade" id="doneModal" tabindex="-1" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content text-center p-3">
                <i class="bi bi-check-circle-fill text-success" style="font-size:3rem"></i>
                <h5 class="mt-2">Transaksi Tersimpan</h5>
                <div class="text-muted small" id="doneInvoice"></div>
                <div class="mt-3" id="doneChangeBox">
                    <div class="text-muted">Kembalian</div>
                    <div class="display-6 fw-bold" id="doneChange"></div>
                </div>
                <button class="btn btn-outline-secondary mt-3" onclick="reprint()"><i class="bi bi-printer me-1"></i>Cetak Ulang</button>
                <button class="btn btn-success mt-2" data-bs-dismiss="modal">Pesanan Baru</button>
            </div>
        </div>
    </div>

    <iframe id="printFrame" style="position:absolute;width:0;height:0;border:0"></iframe>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const PRODUCTS = @json($products);
        const CATS = @json($categories);

        const rp = n => 'Rp ' + Number(n).toLocaleString('id-ID');
        const esc = s => String(s).replace(/[&<>"']/g, c => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        } [c]));
        const $ = id => document.getElementById(id);

        let cart = []; // [{key, product, options, qty}]
        let activeCat = 'all';
        let pending = null; // {p, editIndex}
        const optModal = new bootstrap.Modal($('optModal'));

        /* ---------- Katalog ---------- */
        function renderTabs() {
            const tabs = [{
                id: 'all',
                name: 'Semua Menu'
            }, ...CATS];
            $('catTabs').innerHTML = tabs.map(c =>
                `<button class="btn btn-sm me-1 ${String(c.id) === String(activeCat) ? 'btn-success' : 'btn-outline-success'}"
               onclick="setCat('${c.id}')">${esc(c.name)}</button>`).join('');
        }

        function setCat(id) {
            activeCat = id;
            renderTabs();
            renderMenu();
        }

        function renderMenu() {
            const q = $('search').value.trim().toLowerCase();
            const list = PRODUCTS.filter(p =>
                (activeCat === 'all' || String(p.category_id) === String(activeCat)) &&
                p.name.toLowerCase().includes(q));

            $('menuGrid').innerHTML = list.length ? list.map(p => `
      <div class="col">
        <div class="card menu-card h-100 ${p.is_available ? '' : 'habis'}" onclick="pick(${p.id})">
          ${p.image ? `<img src="${p.image}" class="menu-img card-img-top">` : `<div class="menu-img bg-secondary-subtle card-img-top"></div>`}
          <div class="card-body p-2">
            <div class="fw-semibold small">${esc(p.name)}</div>
            <div class="text-success small">${rp(p.price)}</div>
            ${p.is_available ? '' : '<span class="badge text-bg-danger">Habis</span>'}
          </div>
        </div>
      </div>`).join('') : '<div class="text-muted p-3">Menu tidak ditemukan.</div>';
        }

        /* ---------- Pop-up varian & topping ---------- */
        function pick(id) {
            const p = PRODUCTS.find(x => x.id === id);
            if (!p || !p.is_available) return;
            if (p.options.length === 0) {
                addToCart(p, []);
                return;
            }
            openModal(p, [], null);
        }

        function openModal(p, selected, editIndex) {
            pending = {
                p,
                editIndex
            };
            const v = p.options.filter(o => o.type === 'varian');
            const t = p.options.filter(o => o.type === 'topping');
            const hasSel = v.some(o => selected.includes(o.id));
            let h = '';

            if (v.length) {
                h += '<div class="fw-semibold mb-1">Varian (pilih satu)</div>';
                h += v.map((o, i) => `
        <div class="form-check mb-1">
          <input class="form-check-input" type="radio" name="varian" id="o${o.id}" value="${o.id}"
                 ${(hasSel ? selected.includes(o.id) : i === 0) ? 'checked' : ''}>
          <label class="form-check-label" for="o${o.id}">${esc(o.name)}</label>
        </div>`).join('');
            }
            if (t.length) {
                h += `<div class="fw-semibold mb-1 ${v.length ? 'mt-3' : ''}">Topping (boleh lebih dari satu)</div>`;
                h += t.map(o => `
        <div class="form-check mb-1">
          <input class="form-check-input" type="checkbox" id="o${o.id}" value="${o.id}" ${selected.includes(o.id) ? 'checked' : ''}>
          <label class="form-check-label" for="o${o.id}">${esc(o.name)} <small class="text-muted">+${rp(o.price)}</small></label>
        </div>`).join('');
            }

            $('optTitle').textContent = p.name;
            $('optBody').innerHTML = h;
            $('optSave').textContent = editIndex === null ? 'Tambah ke keranjang' : 'Simpan perubahan';
            optModal.show();
        }

        $('optSave').addEventListener('click', () => {
            const ids = [...$('optBody').querySelectorAll('input:checked')].map(i => +i.value);
            const opts = pending.p.options.filter(o => ids.includes(o.id));
            if (pending.editIndex === null) addToCart(pending.p, opts);
            else updateItem(pending.editIndex, opts);
            optModal.hide();
        });

        /* ---------- Keranjang ---------- */
        const keyOf = (p, opts) => p.id + ':' + opts.map(o => o.id).sort((a, b) => a - b).join(',');
        const unitPrice = it => it.product.price + it.options.reduce((s, o) => s + o.price, 0);

        function addToCart(p, opts) {
            const key = keyOf(p, opts);
            const found = cart.find(c => c.key === key);
            if (found) found.qty++;
            else cart.push({
                key,
                product: p,
                options: opts,
                qty: 1
            });
            renderCart();
        }

        function updateItem(i, opts) {
            const it = cart[i];
            it.options = opts;
            it.key = keyOf(it.product, opts);
            const dup = cart.findIndex((c, j) => j !== i && c.key === it.key);
            if (dup >= 0) {
                cart[dup].qty += it.qty;
                cart.splice(i, 1);
            }
            renderCart();
        }

        function chg(i, d) {
            cart[i].qty += d;
            if (cart[i].qty <= 0) cart.splice(i, 1);
            renderCart();
        }

        function del(i) {
            cart.splice(i, 1);
            renderCart();
        }

        function editItem(i) {
            openModal(cart[i].product, cart[i].options.map(o => o.id), i);
        }

        function clearCart() {
            if (cart.length && confirm('Kosongkan semua pesanan?')) {
                cart = [];
                renderCart();
            }
        }

        function renderCart() {
            $('cartList').innerHTML = cart.length ? cart.map((it, i) => `
      <div class="py-2 border-bottom">
        <div class="d-flex justify-content-between">
          <div class="fw-semibold">${esc(it.product.name)}</div>
          <div class="fw-semibold">${rp(unitPrice(it) * it.qty)}</div>
        </div>
        ${it.options.length ? `<div class="small text-muted">${it.options.map(o => esc(o.name)).join(', ')}</div>` : ''}
        <div class="d-flex justify-content-between align-items-center mt-1">
          <div class="small text-muted">${rp(unitPrice(it))} / porsi</div>
          <div class="d-flex align-items-center gap-1">
            <button class="btn btn-sm btn-outline-secondary" onclick="chg(${i},-1)"><i class="bi bi-dash"></i></button>
            <span class="px-2 fw-bold">${it.qty}</span>
            <button class="btn btn-sm btn-outline-secondary" onclick="chg(${i},1)"><i class="bi bi-plus"></i></button>
            ${it.product.options.length ? `<button class="btn btn-sm btn-outline-primary ms-1" onclick="editItem(${i})"><i class="bi bi-pencil"></i></button>` : ''}
            <button class="btn btn-sm btn-outline-danger" onclick="del(${i})"><i class="bi bi-trash"></i></button>
          </div>
        </div>
      </div>`).join('') : '<div class="text-center text-muted py-5">Belum ada pesanan.<br>Klik menu untuk menambah.</div>';

            const total = cart.reduce((s, it) => s + unitPrice(it) * it.qty, 0);
            const qty = cart.reduce((s, it) => s + it.qty, 0);
            $('subtotal').textContent = rp(total);
            $('badgeTotal').textContent = rp(total);
            $('badgeQty').textContent = qty;
        }

        /* ---------- Tipe pesanan ---------- */
        function syncType() {
            const dine = document.querySelector('input[name=order_type]:checked').value === 'dine_in';
            $('tableNo').style.display = dine ? '' : 'none';
        }
        document.querySelectorAll('input[name=order_type]').forEach(r => r.addEventListener('change', syncType));

        /* ---------- Bayar (sementara, dilengkapi di tahap 5b) ---------- */
        /* ---------- Pembayaran ---------- */
        let payTotal = 0;
        let lastStrukUrl = '';
        const payModal = new bootstrap.Modal($('payModal'));
        const doneModal = new bootstrap.Modal($('doneModal'));
        const getMethod = () => document.querySelector('input[name=pay_method]:checked').value;

        function cartTotal() {
            return cart.reduce((s, it) => s + unitPrice(it) * it.qty, 0);
        }

        function bayar() {
            if (!cart.length) return alert('Keranjang masih kosong.');
            const dine = document.querySelector('input[name=order_type]:checked').value === 'dine_in';
            if (dine && !$('tableNo').value.trim()) return alert('Nomor meja wajib diisi untuk Makan di Sini.');

            payTotal = cartTotal();
            $('payTotal').textContent = rp(payTotal);
            $('cashIn').value = '';
            $('mTunai').checked = true;
            syncPay();
            bootstrap.Offcanvas.getInstance($('cartPanel'))?.hide();
            payModal.show();
        }

        function setCash(n) {
            $('cashIn').value = n;
            syncPay();
        }

        function syncPay() {
            const tunai = getMethod() === 'tunai';
            $('boxTunai').classList.toggle('d-none', !tunai);
            $('boxQris').classList.toggle('d-none', tunai);

            const diff = (+$('cashIn').value || 0) - payTotal;
            $('changeLabel').textContent = diff < 0 ? 'Kurang' : 'Kembalian';
            $('changeVal').textContent = rp(Math.abs(diff));
            $('changeVal').className = diff < 0 ? 'text-danger' : 'text-success';
        }
        document.querySelectorAll('input[name=pay_method]').forEach(r => r.addEventListener('change', syncPay));
        $('cashIn').addEventListener('input', syncPay);

        $('btnSimpan').addEventListener('click', async () => {
            const method = getMethod();
            const cash = +$('cashIn').value || 0;
            if (method === 'tunai' && cash < payTotal) return alert('Uang diterima masih kurang.');

            const btn = $('btnSimpan');
            btn.disabled = true;

            try {
              const res = await fetch('{{ url('/kasir/order') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify({
                            order_type: document.querySelector('input[name=order_type]:checked').value,
                            table_number: $('tableNo').value.trim(),
                            note: $('orderNote').value.trim(),
                            payment_method: method,
                            cash_received: method === 'tunai' ? cash : null,
                            items: cart.map(it => ({
                                product_id: it.product.id,
                                qty: it.qty,
                                option_ids: it.options.map(o => o.id),
                            })),
                        }),
                    });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Gagal menyimpan transaksi.');

                // Berhasil
                payModal.hide();
                lastStrukUrl = data.struk_url;
                $('doneInvoice').textContent = data.invoice_no + ' · ' + rp(data.total);
                $('doneChangeBox').classList.toggle('d-none', data.change === null);
                if (data.change !== null) $('doneChange').textContent = rp(data.change);
                doneModal.show();

                cart = [];
                $('tableNo').value = '';
                $('orderNote').value = '';
                renderCart();
                reprint();
            } catch (e) {
                alert(e.message);
            } finally {
                btn.disabled = false;
            }
        });

        function reprint() {
            if (lastStrukUrl) $('printFrame').src = lastStrukUrl + '?print=1&t=' + Date.now();
        }

        $('search').addEventListener('input', renderMenu);
        renderTabs();
        renderMenu();
        renderCart();
        syncType();
    </script>
</body>

</html>