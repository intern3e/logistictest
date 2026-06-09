<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ประวัติใบเสนอราคา — Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --navy:#1f3a93; --navy2:#16306f; --blue:#1e50c8; --bg:#eef1f6;
    --line:#dfe4ec; --green:#16a34a; --muted:#6b7280; --soft:#f7f9fc;
    --amber:#f59e0b; --red:#dc2626;
  }
  * { box-sizing:border-box; }
  body { margin:0; font-family:'Sarabun',sans-serif; background:var(--bg); color:#1f2937; }

  .topbar {
    background:linear-gradient(90deg,var(--navy2),var(--blue));
    color:#fff; padding:14px 22px; font-weight:600; font-size:18px;
    display:flex; align-items:center; gap:10px;
  }
  .topbar .dot { width:26px; height:26px; border-radius:6px; background:#fff3; display:flex; align-items:center; justify-content:center; }
  .topbar a { color:#fff; text-decoration:none; margin-left:auto; font-size:13px; opacity:.8; }
  .topbar a:hover { opacity:1; }

  .wrap { max-width:1100px; margin:0 auto; padding:20px 18px; }

  /* ---- filter bar ---- */
  .filter-bar {
    display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    background:#fff; border:1px solid var(--line); border-radius:12px;
    padding:12px 16px; margin-bottom:16px;
  }
  .filter-bar input, .filter-bar select {
    padding:8px 12px; border:1px solid var(--line); border-radius:8px;
    font-family:inherit; font-size:13px; background:#fff;
  }
  .filter-bar input[type=text] { flex:1; min-width:180px; }
  .filter-bar select { min-width:120px; }
  .filter-bar .btn-search {
    padding:8px 20px; background:var(--blue); color:#fff; border:none;
    border-radius:8px; font-family:inherit; font-weight:600; font-size:13px; cursor:pointer;
  }
  .filter-bar .btn-search:hover { background:var(--navy2); }
  .filter-bar .btn-reset {
    padding:8px 14px; background:#fff; border:1px solid var(--line); color:var(--muted);
    border-radius:8px; font-family:inherit; font-size:13px; cursor:pointer; text-decoration:none;
  }

  /* ---- table ---- */
  .table-wrap {
    background:#fff; border:1px solid var(--line); border-radius:12px;
    overflow:hidden; box-shadow:0 1px 3px #0000000a;
  }
  table.main { width:100%; border-collapse:collapse; font-size:13px; }
  .main thead th {
    background:linear-gradient(180deg,#f8fafc,#f1f4f9);
    padding:12px 10px; font-weight:700; font-size:11px; color:var(--navy);
    text-align:left; border-bottom:2px solid var(--line);
    text-transform:uppercase; letter-spacing:.04em; white-space:nowrap;
  }
  .main thead th.r { text-align:right; }
  .main thead th.c { text-align:center; }
  .main tbody td { padding:10px 10px; border-bottom:1px solid #f0f2f5; vertical-align:middle; }
  .main tbody tr:last-child td { border-bottom:none; }
  .main tbody tr { cursor:pointer; }
  .main tbody tr:hover td { background:#f0f5ff; }

  .qt-no { font-weight:700; color:var(--blue); white-space:nowrap; }
  .cust-name { font-weight:600; color:#111; }
  .cust-code { font-size:11px; color:var(--muted); }
  .amt { text-align:right; font-weight:700; font-variant-numeric:tabular-nums; color:var(--navy); white-space:nowrap; }
  .date { white-space:nowrap; font-variant-numeric:tabular-nums; color:#555; }
  .items-count { text-align:center; color:var(--muted); font-size:12px; }

  .badge { display:inline-block; padding:2px 10px; border-radius:6px; font-size:11px; font-weight:700; white-space:nowrap; }
  .badge-draft    { background:#fef3c7; color:#92400e; border:1px solid #fde68a; }
  .badge-approved { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
  .badge-cancelled{ background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }

  .act-btn {
    display:inline-flex; align-items:center; gap:4px;
    padding:5px 10px; border-radius:6px; font-size:11px; font-weight:600;
    text-decoration:none; border:1px solid var(--line); color:#374151;
    background:#fff; cursor:pointer; white-space:nowrap;
  }
  .act-btn:hover { background:#f0f5ff; border-color:var(--blue); color:var(--blue); }
  .act-btn.pdf { color:var(--green); border-color:#bbf7d0; }
  .act-btn.pdf:hover { background:#f0fdf4; }

  /* ---- pagination ---- */
  .pagi { display:flex; justify-content:center; gap:4px; padding:14px; }
  .pagi a, .pagi span {
    display:inline-flex; align-items:center; justify-content:center;
    min-width:34px; height:34px; border-radius:8px; font-size:13px; font-weight:600;
    text-decoration:none; border:1px solid var(--line); color:#374151; background:#fff;
  }
  .pagi a:hover { background:#eff6ff; border-color:var(--blue); color:var(--blue); }
  .pagi span.current { background:var(--blue); color:#fff; border-color:var(--blue); }

  /* ---- empty ---- */
  .empty-state { text-align:center; padding:60px 20px; color:var(--muted); }
  .empty-state .em-icon { font-size:48px; margin-bottom:10px; }
  .empty-state .em-title { font-size:16px; font-weight:700; color:#374151; }
  .empty-state .em-sub { font-size:13px; margin-top:4px; }

  /* ===== MODAL ===== */
  .modal-bg {
    display:none; position:fixed; inset:0; z-index:900;
    background:rgba(0,0,0,.45); backdrop-filter:blur(3px);
    align-items:center; justify-content:center; padding:20px;
  }
  .modal-bg.open { display:flex; }
  .modal {
    background:#fff; border-radius:14px; width:100%; max-width:820px;
    max-height:90vh; overflow:hidden; display:flex; flex-direction:column;
    box-shadow:0 20px 60px rgba(0,0,0,.25); animation:modalIn .2s ease-out;
  }
  @keyframes modalIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:none} }

  .modal-head {
    display:flex; align-items:center; gap:10px; padding:16px 22px;
    background:linear-gradient(135deg,#eef2ff,#f8faff); border-bottom:1px solid var(--line);
  }
  .modal-head .mh-title { flex:1; font-size:16px; font-weight:800; color:var(--navy); }
  .modal-head .mh-no { font-size:13px; color:var(--blue); font-weight:600; }
  .modal-head .mh-close {
    width:32px; height:32px; border:none; background:#f3f4f6; border-radius:8px;
    cursor:pointer; font-size:16px; color:#6b7280; display:flex; align-items:center; justify-content:center;
  }
  .modal-head .mh-close:hover { background:#e5e7eb; color:#111; }

  .modal-body { overflow-y:auto; padding:20px 22px; flex:1; }

  /* info grid */
  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:2px 20px; font-size:13px; margin-bottom:18px; }
  .info-grid .ig-label { color:var(--muted); font-size:11px; font-weight:600; margin-top:8px; text-transform:uppercase; letter-spacing:.03em; }
  .info-grid .ig-val { font-weight:600; color:#111; padding-bottom:6px; border-bottom:1px solid #f0f2f5; }
  .info-grid .ig-full { grid-column:1/-1; }

  /* items table in modal */
  .mitbl { width:100%; border-collapse:collapse; font-size:12px; margin-top:14px; }
  .mitbl th {
    background:#f8fafc; padding:8px 8px; font-weight:700; font-size:11px;
    color:var(--navy); text-align:left; border-bottom:2px solid var(--line);
    text-transform:uppercase; letter-spacing:.03em;
  }
  .mitbl th.r { text-align:right; }
  .mitbl th.c { text-align:center; }
  .mitbl td { padding:7px 8px; border-bottom:1px solid #f0f2f5; }
  .mitbl tr:last-child td { border-bottom:none; }
  .mitbl .r { text-align:right; font-variant-numeric:tabular-nums; }
  .mitbl .c { text-align:center; }
  .mitbl .new-tag { font-size:9px; color:#b45309; background:#fef3c7; border:1px solid #fde68a; border-radius:3px; padding:1px 5px; margin-left:4px; }

  .total-box {
    margin-top:14px; margin-left:auto; width:280px;
    border:1px solid var(--line); border-radius:10px; overflow:hidden;
  }
  .total-box .trow { display:flex; justify-content:space-between; padding:8px 14px; font-size:13px; }
  .total-box .trow:not(:last-child) { border-bottom:1px solid #f0f2f5; }
  .total-box .trow .tlbl { color:#555; }
  .total-box .trow .tval { font-weight:700; font-variant-numeric:tabular-nums; }
  .total-box .trow.grand { background:linear-gradient(135deg,#eef2ff,#f0f5ff); }
  .total-box .trow.grand .tlbl { color:var(--navy); font-weight:800; font-size:14px; }
  .total-box .trow.grand .tval { color:var(--navy); font-weight:800; font-size:16px; }

  .modal-foot {
    display:flex; gap:10px; justify-content:flex-end; align-items:center;
    padding:14px 22px; border-top:1px solid var(--line); background:#fafbfc;
  }
  .modal-foot .note-text { flex:1; font-size:12px; color:var(--muted); font-style:italic; }
  .btn-dl {
    display:inline-flex; align-items:center; gap:6px;
    padding:9px 20px; background:var(--green); color:#fff; border:none;
    border-radius:8px; font-family:inherit; font-weight:700; font-size:13px;
    cursor:pointer; text-decoration:none;
  }
  .btn-dl:hover { background:#15803d; }
  .btn-dl.disabled { background:#d1d5db; cursor:not-allowed; }

  @media(max-width:767px) {
    .wrap { padding:10px; }
    .filter-bar { flex-direction:column; }
    .filter-bar input[type=text] { min-width:100%; }
    .table-wrap { overflow-x:auto; }
    table.main { min-width:750px; }
    .modal { max-width:100%; margin:10px; }
    .info-grid { grid-template-columns:1fr; }
    .total-box { width:100%; }
  }
</style>
</head>
<body>

<div class="topbar">
  <span class="dot">📊</span> ประวัติใบเสนอราคา
  <a href="/SoItem">← สร้างใบเสนอราคาใหม่</a>
</div>

<div class="wrap">

  {{-- ========== Filter Bar ========== --}}
  <form class="filter-bar" method="GET" action="/quotations">
    <input type="text" name="search" placeholder="🔍 ค้นหา เลขที่ / ชื่อบริษัท / รหัสลูกค้า ..." value="{{ $search }}">
    <select name="status">
      <option value="">ทุกสถานะ</option>
      <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>แบบร่าง</option>
      <option value="approved"  {{ $status === 'approved'  ? 'selected' : '' }}>อนุมัติ</option>
      <option value="cancelled" {{ $status === 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
    </select>
    <input type="month" name="month" value="{{ $month }}">
    <button type="submit" class="btn-search">ค้นหา</button>
    @if($search || $status || $month)
      <a href="/quotations" class="btn-reset">ล้างตัวกรอง</a>
    @endif
  </form>

  {{-- ========== Table ========== --}}
  <div class="table-wrap">
    @if($quotations->count())
    <table class="main">
      <thead>
        <tr>
          <th>เลขที่</th>
          <th>วันที่</th>
          <th>ลูกค้า</th>
          <th class="c">รายการ</th>
          <th class="r">ยอดรวม (VAT)</th>
          <th class="c">สถานะ</th>
          <th class="c">จัดการ</th>
        </tr>
      </thead>
      <tbody>
        @foreach($quotations as $qt)
        <tr onclick="openModal({{ $qt->id }})">
          <td><span class="qt-no">{{ $qt->quotation_no }}</span></td>
          <td class="date">{{ \Carbon\Carbon::parse($qt->doc_date)->format('d/m/Y') }}</td>
          <td>
            <div class="cust-name">{{ $qt->customer_company }}</div>
            @if($qt->customer_code)
              <div class="cust-code">[{{ $qt->customer_code }}] {{ $qt->contact_name }}</div>
            @endif
          </td>
          <td class="items-count">{{ $qt->items->count() }} รายการ</td>
          <td class="amt">{{ number_format($qt->grand_total, 2) }}</td>
          <td style="text-align:center;">
            @if($qt->status === 'approved')
              <span class="badge badge-approved">อนุมัติ</span>
            @elseif($qt->status === 'cancelled')
              <span class="badge badge-cancelled">ยกเลิก</span>
            @else
              <span class="badge badge-draft">แบบร่าง</span>
            @endif
          </td>
          <td style="text-align:center;white-space:nowrap;" onclick="event.stopPropagation()">
            @if($qt->pdf_path)
              <a href="/quotations/{{ $qt->id }}/pdf" class="act-btn pdf">📥 PDF</a>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @else
    <div class="empty-state">
      <div class="em-icon">📭</div>
      <div class="em-title">ยังไม่มีใบเสนอราคา</div>
      <div class="em-sub">สร้างใบเสนอราคาใหม่ได้ที่ <a href="/SoItem" style="color:var(--blue);">หน้าสร้างใบเสนอราคา</a></div>
    </div>
    @endif
  </div>

  {{-- ========== Pagination ========== --}}
  @if($quotations->hasPages())
  <div class="pagi">
    @if($quotations->onFirstPage())
      <span style="opacity:.4;">‹</span>
    @else
      <a href="{{ $quotations->previousPageUrl() }}">‹</a>
    @endif
    @foreach($quotations->getUrlRange(max(1,$quotations->currentPage()-3),min($quotations->lastPage(),$quotations->currentPage()+3)) as $page => $url)
      @if($page == $quotations->currentPage())
        <span class="current">{{ $page }}</span>
      @else
        <a href="{{ $url }}">{{ $page }}</a>
      @endif
    @endforeach
    @if($quotations->hasMorePages())
      <a href="{{ $quotations->nextPageUrl() }}">›</a>
    @else
      <span style="opacity:.4;">›</span>
    @endif
  </div>
  @endif

</div>

{{-- ========== DETAIL MODAL ========== --}}
<div class="modal-bg" id="modalBg" onclick="if(event.target===this)closeModal()">
  <div class="modal">
    <div class="modal-head">
      <div>
        <div class="mh-title" id="mTitle">ใบเสนอราคา</div>
        <div class="mh-no" id="mNo"></div>
      </div>
      <button class="mh-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body" id="mBody">
      <div style="text-align:center;padding:40px;color:var(--muted);">⏳ กำลังโหลด...</div>
    </div>
    <div class="modal-foot" id="mFoot"></div>
  </div>
</div>

{{-- ★ ส่งข้อมูล quotations ทั้งหมด (รวม items) เป็น JSON ให้ JS ใช้แสดง modal --}}
<script>
const QT_DATA = @json($quotations->getCollection()->keyBy('id'));

function fmt(n) { return Number(n||0).toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function esc(s) { const d=document.createElement('div'); d.textContent=s; return d.innerHTML; }

function openModal(id) {
  const qt = QT_DATA[id];
  if (!qt) return;

  document.getElementById('mTitle').textContent = qt.customer_company;
  document.getElementById('mNo').textContent = qt.quotation_no + '  ·  ' + formatDate(qt.doc_date);

  /* ---- info ---- */
  let html = '<div class="info-grid">';
  html += ig('รหัสลูกค้า',        qt.customer_code || '-');
  html += ig('ผู้ติดต่อ',          qt.contact_name || '-');
  html += ig('เบอร์โทร',          qt.customer_tel || '-');
  html += ig('เลขผู้เสียภาษี',     qt.customer_tax || '-');
  html += ig('สาขา',              qt.customer_branch || '-');
  html += ig('ยืนราคา',           qt.valid_days ? qt.valid_days + ' วัน' : '-');
  html += ig('Expire Date',       qt.expire_date ? formatDate(qt.expire_date) : '-');
  html += ig('เครดิต',            qt.credit_days ? qt.credit_days + ' วัน' : '-');
  html += igFull('ที่อยู่',        qt.customer_address || '-');
  if (qt.note) html += igFull('หมายเหตุ', qt.note);
  html += '</div>';

  /* ---- items table ---- */
  const items = qt.items || [];
  html += '<table class="mitbl"><thead><tr>';
  html += '<th class="c" style="width:40px">#</th><th>รายการสินค้า</th>';
  html += '<th class="c" style="width:70px">จำนวน</th><th class="c" style="width:60px">หน่วย</th>';
  html += '<th class="r" style="width:100px">ราคา/หน่วย</th><th class="r" style="width:110px">จำนวนเงิน</th>';
  html += '</tr></thead><tbody>';

  items.forEach((it, i) => {
    const amt = (it.qty||0) * (it.unit_price||0);
    const newTag = it.is_new ? '<span class="new-tag">ใหม่</span>' : '';
    html += '<tr>';
    html += '<td class="c">' + (i+1) + '</td>';
    html += '<td>' + esc(it.description||'-') + newTag + '</td>';
    html += '<td class="c">' + fmt(it.qty) + '</td>';
    html += '<td class="c">' + esc(it.unit||'') + '</td>';
    html += '<td class="r">' + fmt(it.unit_price) + '</td>';
    html += '<td class="r" style="font-weight:700;color:var(--navy);">' + fmt(amt) + '</td>';
    html += '</tr>';
  });
  html += '</tbody></table>';

  /* ---- totals ---- */
  html += '<div class="total-box">';
  html += trow('รวมก่อน VAT', fmt(qt.gross_amount));
  html += trow('VAT 7%',      fmt(qt.vat_amount));
  html += trow('รวมทั้งสิ้น',  fmt(qt.grand_total), true);
  html += '</div>';

  document.getElementById('mBody').innerHTML = html;

  /* ---- footer ---- */
  let foot = '';
  if (qt.note) foot += '<div class="note-text">📝 ' + esc(qt.note) + '</div>';
  if (qt.pdf_path) {
    foot += '<a href="/quotations/' + qt.id + '/pdf" class="btn-dl">📥 ดาวน์โหลด PDF</a>';
  } else {
    foot += '<span class="btn-dl disabled">ไม่มีไฟล์ PDF</span>';
  }
  document.getElementById('mFoot').innerHTML = foot;

  document.getElementById('modalBg').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeModal() {
  document.getElementById('modalBg').classList.remove('open');
  document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

/* helpers */
function ig(label, val) {
  return '<div class="ig-label">' + label + '</div><div class="ig-val">' + esc(val) + '</div>';
}
function igFull(label, val) {
  return '<div class="ig-label ig-full">' + label + '</div><div class="ig-val ig-full">' + esc(val) + '</div>';
}
function trow(label, val, grand) {
  const cls = grand ? ' grand' : '';
  return '<div class="trow' + cls + '"><span class="tlbl">' + label + '</span><span class="tval">' + val + '</span></div>';
}
function formatDate(d) {
  if (!d) return '-';
  const p = d.split('-');
  if (p.length !== 3) return d;
  return p[2] + '/' + p[1] + '/' + p[0];
}
</script>

</body>
</html>