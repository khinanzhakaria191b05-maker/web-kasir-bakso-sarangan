@extends('layouts.admin')
@section('title', 'Dashboard')
@section('content')
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h3 class="mb-0">Dashboard Laporan</h3>
    <div class="btn-group btn-group-sm">
      <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-success">Hari Ini</a>
      <a href="{{ route('admin.dashboard', ['from' => today()->startOfMonth()->toDateString(), 'to' => today()->toDateString()]) }}" class="btn btn-outline-success">Bulan Ini</a>
      <a href="{{ route('admin.dashboard', ['from' => today()->subMonthNoOverflow()->startOfMonth()->toDateString(), 'to' => today()->subMonthNoOverflow()->endOfMonth()->toDateString()]) }}" class="btn btn-outline-success">Bulan Lalu</a>
    </div>
  </div>

  <form method="GET" class="row g-2 align-items-end mb-4">
    <div class="col-6 col-md-3">
      <label class="form-label small mb-0">Dari</label>
      <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
    </div>
    <div class="col-6 col-md-3">
      <label class="form-label small mb-0">Sampai</label>
      <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
    </div>
    <div class="col-12 col-md-2"><button class="btn btn-success w-100">Tampilkan</button></div>
  </form>

  <div class="text-muted small mb-2">
    Periode: {{ $from->translatedFormat('d M Y') }}@if($from->toDateString() !== $to->toDateString()) – {{ $to->translatedFormat('d M Y') }}@endif
  </div>

  {{-- Kartu ringkasan --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
      <div class="card h-100 border-success"><div class="card-body">
        <div class="text-muted small">Total Omzet</div>
        <div class="fs-4 fw-bold text-success">Rp {{ number_format($omzet, 0, ',', '.') }}</div>
      </div></div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted small">Jumlah Transaksi</div>
        <div class="fs-4 fw-bold">{{ number_format($trx, 0, ',', '.') }}</div>
      </div></div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted small">Porsi Terjual</div>
        <div class="fs-4 fw-bold">{{ number_format($porsi, 0, ',', '.') }}</div>
      </div></div>
    </div>
    <div class="col-6 col-lg-3">
      <div class="card h-100"><div class="card-body">
        <div class="text-muted small">Rata-rata / Transaksi</div>
        <div class="fs-4 fw-bold">Rp {{ number_format($trx ? $omzet / $trx : 0, 0, ',', '.') }}</div>
      </div></div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card h-100"><div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="text-muted small"><i class="bi bi-cash-coin me-1"></i>Pendapatan Tunai</div>
          <div class="fs-5 fw-bold">Rp {{ number_format($tunai, 0, ',', '.') }}</div></div>
        <span class="badge text-bg-success">{{ $omzet ? round($tunai / $omzet * 100) : 0 }}%</span>
      </div></div>
    </div>
    <div class="col-12 col-md-6">
      <div class="card h-100"><div class="card-body d-flex justify-content-between align-items-center">
        <div><div class="text-muted small"><i class="bi bi-qr-code me-1"></i>Pendapatan QRIS / Transfer</div>
          <div class="fs-5 fw-bold">Rp {{ number_format($qris, 0, ',', '.') }}</div></div>
        <span class="badge text-bg-primary">{{ $omzet ? round($qris / $omzet * 100) : 0 }}%</span>
      </div></div>
    </div>
  </div>

  {{-- Grafik --}}
  @if($harian->count() > 1)
    <div class="card mb-4"><div class="card-body">
      <h6 class="mb-3">Omzet per Hari</h6>
      <div style="height:260px"><canvas id="chartOmzet"></canvas></div>
    </div></div>
  @endif

  <div class="row g-3">
    <div class="col-12 col-xl-7">
      <div class="card h-100">
        <div class="card-header bg-white fw-semibold">Rekap Harian</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Tanggal</th><th class="text-end">Trx</th><th class="text-end">Tunai</th><th class="text-end">QRIS</th><th class="text-end">Omzet</th></tr></thead>
            <tbody>
              @forelse($harian as $h)
                <tr>
                  <td>{{ \Carbon\Carbon::parse($h->tgl)->translatedFormat('D, d M Y') }}</td>
                  <td class="text-end">{{ $h->trx }}</td>
                  <td class="text-end">{{ number_format($h->tunai, 0, ',', '.') }}</td>
                  <td class="text-end">{{ number_format($h->qris, 0, ',', '.') }}</td>
                  <td class="text-end fw-semibold">{{ number_format($h->omzet, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada transaksi di periode ini.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-5">
      <div class="card h-100">
        <div class="card-header bg-white fw-semibold">Menu Terlaris</div>
        <div class="table-responsive">
          <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>#</th><th>Menu</th><th class="text-end">Porsi</th><th class="text-end">Omzet</th></tr></thead>
            <tbody>
              @forelse($terlaris as $i => $t)
                <tr>
                  <td>{{ $i + 1 }}</td>
                  <td>{{ $t->product_name }}</td>
                  <td class="text-end">{{ $t->porsi }}</td>
                  <td class="text-end">{{ number_format($t->omzet, 0, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-4">Belum ada data.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  @if($harian->count() > 1)
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script>
      new Chart(document.getElementById('chartOmzet'), {
        type: 'bar',
        data: {
          labels: @json($harian->map(fn ($h) => \Carbon\Carbon::parse($h->tgl)->format('d/m'))),
          datasets: [
            { label: 'Tunai', data: @json($harian->pluck('tunai')), backgroundColor: '#198754' },
            { label: 'QRIS', data: @json($harian->pluck('qris')), backgroundColor: '#0d6efd' },
          ]
        },
        options: {
          maintainAspectRatio: false,
          scales: {
            x: { stacked: true },
            y: { stacked: true, ticks: { callback: v => 'Rp ' + Number(v).toLocaleString('id-ID') } }
          },
          plugins: { tooltip: { callbacks: { label: c => c.dataset.label + ': Rp ' + Number(c.raw).toLocaleString('id-ID') } } }
        }
      });
    </script>
  @endif
@endsection