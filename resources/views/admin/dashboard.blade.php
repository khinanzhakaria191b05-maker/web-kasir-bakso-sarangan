@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')

@php
$today = today();
$curFrom = $from->toDateString();
$curTo = $to->toDateString();

$ranges = [
'Hari ini' => [$today->toDateString(), $today->toDateString()],
'7 hari' => [$today->copy()->subDays(6)->toDateString(), $today->toDateString()],
'Bulan ini' => [$today->copy()->startOfMonth()->toDateString(), $today->toDateString()],
'Bulan lalu' => [
$today->copy()->subMonthNoOverflow()->startOfMonth()->toDateString(),
$today->copy()->subMonthNoOverflow()->endOfMonth()->toDateString(),
],
];
$isPreset = collect($ranges)->contains(fn ($r) => $r[0] === $curFrom && $r[1] === $curTo);

$periode = $from->translatedFormat('d M Y');
if ($curFrom !== $curTo) { $periode .= ' – ' . $to->translatedFormat('d M Y'); }

$pctTunai = $omzet > 0 ? round($tunai / $omzet * 100) : 0;
$pctQris = $omzet > 0 ? 100 - $pctTunai : 0;
$rp = fn ($n) => 'Rp ' . number_format($n, 0, ',', '.');

$chartLabels = $harian->map(fn ($h) => \Carbon\Carbon::parse($h->tgl)->format('d/m'))->values();
$chartTunai = $harian->pluck('tunai')->map(fn ($v) => (int) $v)->values();
$chartQris = $harian->pluck('qris')->map(fn ($v) => (int) $v)->values();
@endphp

{{-- Judul dan filter --}}
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
  <div>
    <h1 class="mb-0">Dashboard</h1>
    <div class="text-muted small">{{ $periode }}</div>
  </div>
  <div class="d-flex flex-wrap gap-2">
    <div class="btn-group btn-group-sm">
      @foreach($ranges as $name => [$f, $t])
      <a href="{{ route('admin.dashboard', ['from' => $f, 'to' => $t]) }}"
        class="btn {{ $curFrom === $f && $curTo === $t ? 'btn-success' : 'btn-outline-success' }}">{{ $name }}</a>
      @endforeach
    </div>
    <button class="btn btn-sm {{ $isPreset ? 'btn-outline-secondary' : 'btn-secondary' }}" type="button"
      data-bs-toggle="collapse" data-bs-target="#customRange">
      <i class="bi bi-calendar-range me-1"></i>Pilih tanggal
    </button>
  </div>
</div>

<div class="collapse {{ $isPreset ? '' : 'show' }} mb-4" id="customRange">
  <form method="GET" class="card card-body">
    <div class="row g-2 align-items-end">
      <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Dari</label>
        <input type="date" name="from" value="{{ $curFrom }}" class="form-control">
      </div>
      <div class="col-6 col-md-3">
        <label class="form-label small mb-1">Sampai</label>
        <input type="date" name="to" value="{{ $curTo }}" class="form-control">
      </div>
      <div class="col-12 col-md-2">
        <button class="btn btn-success w-100">Tampilkan</button>
      </div>
    </div>
  </form>
</div>

@if($trx === 0)
<div class="alert alert-light border mb-4">Belum ada transaksi pada periode ini.</div>
@endif

{{-- Baris 1: angka utama --}}
<div class="row g-3 mb-3">
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Total omzet</div>
        <div class="fs-4 fw-semibold text-success">{{ $rp($omzet) }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Jumlah transaksi</div>
        <div class="fs-4 fw-semibold">{{ number_format($trx, 0, ',', '.') }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Porsi terjual</div>
        <div class="fs-4 fw-semibold">{{ number_format($porsi, 0, ',', '.') }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1">Rata-rata per transaksi</div>
        <div class="fs-4 fw-semibold">{{ $rp($trx ? $omzet / $trx : 0) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Baris 2: pembayaran, PPN, diskon --}}
<div class="row g-3 mb-4">
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <span class="text-muted small"><i class="bi bi-cash-coin me-1"></i>Tunai</span>
          <span class="badge text-bg-success">{{ $pctTunai }}%</span>
        </div>
        <div class="fs-5 fw-semibold">{{ $rp($tunai) }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-1">
          <span class="text-muted small"><i class="bi bi-qr-code me-1"></i>QRIS / transfer</span>
          <span class="badge text-bg-primary">{{ $pctQris }}%</span>
        </div>
        <div class="fs-5 fw-semibold">{{ $rp($qris) }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1"><i class="bi bi-percent me-1"></i>PPN terkumpul</div>
        <div class="fs-5 fw-semibold">{{ $rp($ppn) }}</div>
      </div>
    </div>
  </div>
  <div class="col-6 col-lg-3">
    <div class="card h-100">
      <div class="card-body">
        <div class="text-muted small mb-1"><i class="bi bi-tag me-1"></i>Diskon diberikan</div>
        <div class="fs-5 fw-semibold text-danger">{{ $rp($diskon) }}</div>
      </div>
    </div>
  </div>
</div>

{{-- Grafik --}}
@if($harian->count() > 1)
<div class="card mb-4">
  <div class="card-header">Omzet per hari</div>
  <div class="card-body">
    <div style="height:280px">
      <canvas id="chartOmzet"
        data-labels="{{ json_encode($chartLabels) }}"
        data-tunai="{{ json_encode($chartTunai) }}"
        data-qris="{{ json_encode($chartQris) }}"></canvas>
    </div>
  </div>
</div>
@endif

{{-- Tabel --}}
<div class="row g-3">
  <div class="col-12 col-xl-7">
    <div class="card h-100">
      <div class="card-header">Rekap harian</div>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th>Tanggal</th>
              <th class="text-end">Transaksi</th>
              <th class="text-end">Tunai</th>
              <th class="text-end">QRIS</th>
              <th class="text-end">Omzet</th>
            </tr>
          </thead>
          <tbody>
            @forelse($harian as $h)
            <tr>
              <td class="text-nowrap">{{ \Carbon\Carbon::parse($h->tgl)->translatedFormat('D, d M Y') }}</td>
              <td class="text-end">{{ $h->trx }}</td>
              <td class="text-end">{{ number_format($h->tunai, 0, ',', '.') }}</td>
              <td class="text-end">{{ number_format($h->qris, 0, ',', '.') }}</td>
              <td class="text-end fw-semibold">{{ number_format($h->omzet, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="text-center text-muted py-4">Belum ada transaksi.</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-12 col-xl-5">
    <div class="card h-100">
      <div class="card-header">Menu terlaris</div>
      <div class="table-responsive">
        <table class="table align-middle">
          <thead>
            <tr>
              <th style="width:44px">#</th>
              <th>Menu</th>
              <th class="text-end">Porsi</th>
              <th class="text-end">Omzet</th>
            </tr>
          </thead>
          <tbody>
            @forelse($terlaris as $i => $t)
            <tr>
              <td class="text-muted">{{ $i + 1 }}</td>
              <td>{{ $t->product_name }}</td>
              <td class="text-end fw-semibold">{{ $t->porsi }}</td>
              <td class="text-end">{{ number_format($t->omzet, 0, ',', '.') }}</td>
            </tr>
            @empty
            <tr>
              <td colspan="4" class="text-center text-muted py-4">Belum ada data.</td>
            </tr>
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
  var el = document.getElementById('chartOmzet');
  var rupiah = function(v) {
    return 'Rp ' + Number(v).toLocaleString('id-ID');
  };

  new Chart(el, {
    type: 'bar',
    data: {
      labels: JSON.parse(el.dataset.labels),
      datasets: [{
          label: 'Tunai',
          data: JSON.parse(el.dataset.tunai),
          backgroundColor: '#198754'
        },
        {
          label: 'QRIS',
          data: JSON.parse(el.dataset.qris),
          backgroundColor: '#2f6fb3'
        }
      ]
    },
    options: {
      maintainAspectRatio: false,
      scales: {
        x: {
          stacked: true,
          grid: {
            display: false
          }
        },
        y: {
          stacked: true,
          ticks: {
            callback: rupiah
          }
        }
      },
      plugins: {
        legend: {
          position: 'bottom'
        },
        tooltip: {
          callbacks: {
            label: function(c) {
              return c.dataset.label + ': ' + rupiah(c.raw);
            }
          }
        }
      }
    }
  });
</script>
@endif

@endsection