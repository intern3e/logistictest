<!DOCTYPE html>
<html lang="th">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>ระบบบริหารการจัดส่ง</title>
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@300;400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
  <style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --blue-950:#041830;--blue-900:#073260;--blue-800:#0c4a8c;--blue-700:#1560b8;
  --blue-600:#1a75d8;--blue-500:#2e8bef;--blue-400:#60aaff;--blue-300:#93c5fd;
  --blue-200:#bfdbfe;--blue-100:#dbeafe;--blue-50:#eff6ff;
  --gray-900:#111827;--gray-800:#1f2937;--gray-700:#374151;--gray-600:#4b5563;
  --gray-500:#6b7280;--gray-400:#9ca3af;--gray-300:#d1d5db;--gray-200:#e5e7eb;
  --gray-100:#f3f4f6;--gray-50:#f9fafb;
  --white:#ffffff;--surface:#f5f8ff;
  --green-700:#15803d;--green-600:#16a34a;--green-100:#dcfce7;--green-50:#f0fdf4;
  --amber-700:#b45309;--amber-100:#fef3c7;--amber-50:#fffbeb;
  --red-700:#b91c1c;--red-600:#dc2626;--red-100:#fee2e2;--red-50:#fef2f2;
  --r:6px;--rl:10px;
}
html,body{height:100%}
body{font-family:'IBM Plex Sans Thai',sans-serif;background:var(--surface);color:var(--gray-900);font-size:14px;line-height:1.6;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:var(--blue-900);height:56px;flex-shrink:0;display:flex;align-items:center;justify-content:space-between;padding:0 24px;position:sticky;top:0;z-index:100;border-bottom:1px solid var(--blue-800);}
.brand{display:flex;align-items:center;gap:10px}
.brand-mark{width:32px;height:32px;background:var(--blue-600);border-radius:8px;display:flex;align-items:center;justify-content:center;border:1px solid var(--blue-500);}
.brand-mark svg{width:15px;height:15px;color:#fff}
.brand-text .name{font-size:13.5px;font-weight:600;color:#fff;letter-spacing:0.01em}
.brand-text .sub{font-size:10px;color:var(--blue-300);letter-spacing:0.08em;margin-top:1px;font-weight:400}
.topbar-right{display:flex;align-items:center;gap:6px}
.tsep{width:1px;height:20px;background:var(--blue-800);margin:0 4px}
.hdr-date{font-family:'IBM Plex Mono',monospace;font-size:10.5px;color:var(--blue-300);letter-spacing:0.03em}
main{flex:1;display:flex;flex-direction:column;padding:20px 24px;gap:12px}
.filter-bar{background:var(--white);border:1px solid var(--gray-200);border-radius:var(--rl);padding:11px 16px;display:flex;align-items:center;gap:10px;flex-wrap:wrap;}
.filter-label{font-size:11px;font-weight:600;color:var(--gray-500);text-transform:uppercase;letter-spacing:0.1em;white-space:nowrap;display:flex;align-items:center;gap:5px}
.filter-label svg{width:10px;height:10px}
.fsep{width:1px;height:22px;background:var(--gray-200)}
.fi{height:32px;border:1px solid var(--gray-300);border-radius:var(--r);padding:0 10px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;color:var(--gray-900);background:var(--gray-50);outline:none;transition:border-color .15s,box-shadow .15s;}
.fi:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(26,117,216,.12);background:var(--white)}
select.fi{appearance:none;cursor:pointer;padding-right:26px;min-width:150px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M0 0l5 6 5-6z' fill='%236b7280'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 8px center;}
.btn-go{height:32px;background:var(--blue-700);color:#fff;border:none;border-radius:var(--r);padding:0 14px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;font-weight:500;cursor:pointer;display:flex;align-items:center;gap:6px;transition:background .15s;white-space:nowrap;}
.btn-go:hover{background:var(--blue-800)}
.btn-go svg{width:11px;height:11px}
.btn-rst{height:32px;background:transparent;color:var(--gray-600);border:1px solid var(--gray-300);border-radius:var(--r);padding:0 12px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:13px;cursor:pointer;display:flex;align-items:center;gap:5px;transition:all .15s;text-decoration:none;}
.btn-rst:hover{background:var(--gray-100);border-color:var(--gray-400)}
.btn-rst svg{width:11px;height:11px}
.tbl-card{background:var(--white);border:1px solid var(--gray-200);border-radius:var(--rl);flex:1;display:flex;flex-direction:column;min-height:0;overflow:hidden;}
.tbl-head{padding:11px 16px;border-bottom:1px solid var(--gray-200);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;flex-wrap:wrap;gap:8px;background:var(--white);}
.tbl-head-l{display:flex;align-items:center;gap:8px}
.tbl-title{font-size:13px;font-weight:600;color:var(--gray-800)}
.rec-badge{background:var(--blue-100);color:var(--blue-800);border-radius:20px;padding:2px 10px;font-size:11px;font-weight:600;font-family:'IBM Plex Mono',monospace;letter-spacing:0.02em;}
.srch-wrap{position:relative}
.srch-wrap svg{position:absolute;left:9px;top:50%;transform:translateY(-50%);color:var(--gray-400);pointer-events:none;width:12px;height:12px}
.srch-inp{height:30px;border:1px solid var(--gray-300);border-radius:var(--r);padding:0 10px 0 28px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:12.5px;color:var(--gray-900);background:var(--gray-50);outline:none;width:200px;transition:all .15s;}
.srch-inp:focus{border-color:var(--blue-500);box-shadow:0 0 0 3px rgba(26,117,216,.1);width:230px;background:var(--white)}
.tbl-scroll{flex:1;overflow:auto}
table{width:100%;border-collapse:collapse;table-layout:fixed}
col.c-n{width:44px}col.c-d{width:100px}col.c-drv{width:100px}col.c-nm{width:130px}
col.c-bill{width:140px}col.c-note{width:180px}col.c-st{width:240px}
thead th{padding:10px 14px;font-size:10.5px;font-weight:600;color:#ffffff;text-transform:uppercase;letter-spacing:0.1em;background:#1e3a8a;border-bottom:2px solid #020202;white-space:nowrap;text-align:center;position:sticky;top:0;z-index:2;}
thead th.th-hl{background:#1e3a8a;color:#eff6ff}
tbody tr{border-bottom:1px solid var(--gray-100);transition:background .08s}
tbody tr:last-child{border-bottom:none}
tbody tr:hover{background:var(--blue-50)}
tbody td{padding:10px 14px;font-size:13px;vertical-align:middle;color:var(--gray-700);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;text-align:center}
tbody td.td-note{text-align:left}
.row-num{font-family:'IBM Plex Mono',monospace;font-size:11px;color:var(--gray-400);font-weight:500}
.cell-date{font-family:'IBM Plex Mono',monospace;font-size:11.5px;color:var(--gray-600);letter-spacing:0.02em}
.cell-name{font-weight:600;color:var(--blue-800);font-size:13px}
.cell-bill{display:inline-flex;align-items:center;font-family:'IBM Plex Mono',monospace;font-size:11px;font-weight:600;color:var(--red-700);background:var(--red-50);border:1px solid var(--red-200);border-radius:5px;padding:3px 9px;letter-spacing:0.04em;}
.cell-note{font-size:12px;color:var(--gray-500);display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cell-note.empty{color:var(--gray-300)}

/* STATUS BADGE */
.st-badge{display:inline-flex;align-items:center;gap:5px;border-radius:6px;padding:4px 10px;font-size:11.5px;font-weight:600;border:1px solid;white-space:nowrap;}
.st-badge svg{width:11px;height:11px;flex-shrink:0}
.st-ng{background:var(--gray-50);color:var(--gray-600);border-color:var(--gray-300)}
.st-pending{background:var(--amber-50);color:var(--amber-700);border-color:#fcd34d}

.st-cell{display:flex;flex-direction:column;align-items:stretch;gap:6px;width:100%}
.nb-wrap{display:flex;align-items:stretch;gap:5px;width:100%}
.nb-inp{height:30px;border:1px solid var(--gray-300);border-radius:5px;padding:0 9px;font-family:'IBM Plex Mono',monospace;font-size:11.5px;font-weight:500;color:var(--blue-800);background:var(--white);outline:none;flex:1;min-width:0;transition:border-color .12s;}
.nb-inp::placeholder{color:var(--gray-400);font-family:'IBM Plex Sans Thai',sans-serif;font-size:11px}
.nb-inp:focus{border-color:var(--blue-500);box-shadow:0 0 0 2px rgba(26,117,216,.12)}
.nb-save{height:30px;background:var(--blue-700);color:#fff;border:none;border-radius:5px;padding:0 12px;font-family:'IBM Plex Sans Thai',sans-serif;font-size:12px;font-weight:500;cursor:pointer;white-space:nowrap;transition:background .12s;flex-shrink:0;}
.nb-save:hover{background:var(--blue-800)}

/* Locked display */
.nb-locked{display:flex;align-items:center;gap:7px;background:var(--amber-50);border:1px solid #fcd34d;border-radius:5px;padding:4px 10px;}
.nb-locked-val{font-family:'IBM Plex Mono',monospace;font-size:11.5px;font-weight:600;color:var(--amber-700);flex:1;}
.nb-lock-ico{color:var(--amber-700);flex-shrink:0}
.nb-lock-ico svg{width:12px;height:12px}

.tbl-foot{padding:9px 16px;border-top:1px solid var(--gray-200);background:var(--gray-50);display:flex;justify-content:space-between;align-items:center;font-size:11px;color:var(--gray-500);font-family:'IBM Plex Mono',monospace;letter-spacing:0.02em;flex-shrink:0;}
.empty-cell{padding:64px 24px !important;text-align:center !important;white-space:normal !important}
.empty-ico{width:44px;height:44px;background:var(--gray-100);border:1px solid var(--gray-200);border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;color:var(--gray-400)}
.empty-t{font-size:13px;font-weight:600;color:var(--gray-700);margin-bottom:4px}
.empty-s{font-size:12px;color:var(--gray-400)}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(10px);background:var(--gray-900);color:#fff;padding:9px 18px;border-radius:8px;font-size:12px;font-weight:500;opacity:0;transition:all .2s ease;z-index:999;pointer-events:none;white-space:nowrap;border-left:3px solid var(--blue-400);letter-spacing:0.01em;}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0)}
@media(max-width:768px){
  .topbar,main{padding-left:14px;padding-right:14px}
  .filter-bar{flex-direction:column;align-items:stretch}
  .fi{width:100%}.fsep{display:none}
  .hdr-date{display:none}
}
  </style>
</head>
<body>
<header class="topbar">
  <div class="brand">
    <div class="brand-mark">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg>
    </div>
    <div class="brand-text">
      <div class="name">ระบบบริหารการจัดส่ง</div>
      <div class="sub">Delivery Management System</div>
    </div>
  </div>
  <div class="topbar-right">
    <span class="hdr-date" id="hdr-date"></span>
    <div class="tsep"></div>
  </div>
</header>

<main>
  <div class="filter-bar">
    <div class="filter-label">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
      กรอง
    </div>
    <div class="fsep"></div>
    <form method="GET" action="{{ route('deliverytrack') }}" style="display:contents">
      <input type="date" name="date" id="f-date" class="fi" style="width:148px" value="{{ $date ?? '' }}" onchange="this.form.submit()">

      <select name="driver" id="f-driver" class="fi" onchange="this.form.submit()">
        <option value="">— คนขับทั้งหมด —</option>
        @foreach($drivers as $d)
          <option value="{{ $d }}" {{ ($driver ?? '') === $d ? 'selected' : '' }}>{{ $d }}</option>
        @endforeach
      </select>

      <select name="status" id="f-status" class="fi" onchange="this.form.submit()">
        <option value="ng"        {{ ($status ?? 'ng') === 'ng'        ? 'selected' : '' }}>🔴 รอกรอกบิล (NG)</option>
        <option value="pending"   {{ ($status ?? 'ng') === 'pending'   ? 'selected' : '' }}>🟡 บันทึกแล้ว (Pending)</option>
        <option value="completed" {{ ($status ?? 'ng') === 'completed' ? 'selected' : '' }}>🟢 เสร็จสิ้น (Completed)</option>
      </select>

      {{-- <button type="submit" class="btn-go">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        ค้นหา
      </button> --}}
    </form>
    <a href="{{ route('deliverytrack') }}" class="btn-rst">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
      รีเซ็ต
    </a>
  </div>

  <div class="tbl-card">
    <div class="tbl-head">
      <div class="tbl-head-l">
        <span class="tbl-title">รายการทั้งหมด</span>
        <span class="rec-badge">{{ $shipments->count() }} รายการ</span>
      </div>
      <div class="srch-wrap">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        <input type="text" class="srch-inp" placeholder="ค้นหา..." oninput="doSearch(this.value)">
      </div>
    </div>

    <div class="tbl-scroll">
      <table>
        <colgroup>
          <col class="c-n"><col class="c-d"><col class="c-drv"><col class="c-nm">
          <col class="c-bill"><col class="c-note"><col class="c-st">
        </colgroup>
        <thead>
          <tr>
            <th>#</th>
            <th>วันที่</th>
            <th>คนขับ</th>
            <th>ชื่อ Sale</th>
            <th>เลขที่บิล</th>
            <th class="th-hl">หมายเหตุ</th>
            <th>เลขบิลใหม่ / สถานะ</th>
          </tr>
        </thead>
        <tbody id="tbody">
          @forelse($shipments as $i => $r)
            @php $isPending = $r->status === 'pending'; @endphp
            <tr
              data-bill="{{ strtolower($r->bill_no ?? '') }}"
              data-driver="{{ strtolower($r->driver_name ?? '') }}"
              data-seller="{{ strtolower($r->seller_name ?? '') }}"
              data-customer="{{ strtolower($r->customer_name ?? '') }}"
              data-note="{{ strtolower($r->note ?? '') }}"
              data-status="{{ $r->status }}"
              >
              <td><span class="row-num">{{ $i + 1 }}</span></td>
              <td><span class="cell-date">{{ $r->ng_date ? $r->ng_date->format('d/m/Y') : '—' }}</span></td>
              <td><span style="font-size:12.5px;color:var(--gray-700)">{{ $r->driver_name ?: '—' }}</span></td>
              <td><span class="cell-name">{{ $r->seller_name ?: '—' }}</span></td>
              <td><span class="cell-bill">{{ $r->bill_no ?: '—' }}</span></td>
              <td class="td-note" title="{{ $r->note ?? '' }}">
                @if($r->note)
                  <span class="cell-note">{{ $r->note }}</span>
                @else
                  <span class="cell-note empty">—</span>
                @endif
              </td>
              <td>
                <div class="st-cell" id="stcell-{{ $r->id }}">
                  @php $isPending = $r->status === 'pending'; $isCompleted = $r->status === 'completed'; @endphp

                  @if($isCompleted)
                    <span class="st-badge" style="background:var(--green-100);color:var(--green-700);border-color:#86efac">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                      เสร็จสิ้น
                    </span>
                    @if($r->new_bill_no)
                    <div class="nb-locked" style="background:var(--green-50);border-color:#86efac">
                      <span class="nb-lock-ico" style="color:var(--green-700)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                      </span>
                      <span class="nb-locked-val" style="color:var(--green-700)">{{ $r->new_bill_no }}</span>
                    </div>
                    @endif

                  @elseif($isPending)
                    <span class="st-badge st-pending">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                      Pending
                    </span>
                    <div class="nb-locked">
                      <span class="nb-lock-ico">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/></svg>
                      </span>
                      <span class="nb-locked-val">{{ $r->new_bill_no }}</span>
                    </div>

                  @else
                    <span class="st-badge st-ng">
                      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                      NG — รอกรอกบิล
                    </span>
                    <div class="nb-wrap">
                      <input
                        class="nb-inp"
                        id="nb-input-{{ $r->id }}"
                        type="text"
                        placeholder="กรอกเลขบิลใหม่..."
                        onkeydown="if(event.key==='Enter')saveNewBill({{ $r->id }})"
                      >
                      <button class="nb-save" onclick="saveNewBill({{ $r->id }})">บันทึก</button>
                    </div>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="empty-cell">
                <div class="empty-ico">
                  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11a2 2 0 012 2v3"/><rect x="9" y="11" width="14" height="10" rx="2"/><circle cx="12" cy="20" r="1"/><circle cx="20" cy="20" r="1"/></svg>
                </div>
                <div class="empty-t">ไม่พบรายการ</div>
                <div class="empty-s">ไม่มีข้อมูลที่ตรงกับเงื่อนไข</div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="tbl-foot">
      <span>แสดง {{ $shipments->count() }} รายการ</span>
      <span id="ft-time">—</span>
    </div>
  </div>
</main>

<div class="toast" id="toast"></div>

<script>
(function(){
  const now = new Date();
  document.getElementById('hdr-date').textContent =
    now.toLocaleDateString('th-TH', {weekday:'long', year:'numeric', month:'long', day:'numeric'});
  document.getElementById('ft-time').textContent = 'อัพเดต ' + now.toLocaleTimeString('th-TH');
})();

function doSearch(q) {
  q = q.toLowerCase().trim();
  document.querySelectorAll('#tbody tr[data-bill]').forEach(tr => {
    if (!q) { tr.style.display = ''; return; }
    const hit = ['bill','driver','seller','customer','note'].some(k =>
      (tr.dataset[k] || '').includes(q)
    );
    tr.style.display = hit ? '' : 'none';
  });
}

async function saveNewBill(id) {
  const inp = document.getElementById('nb-input-' + id);
  const val = inp ? inp.value.trim() : '';
  if (!val) {
    showToast('⚠ กรุณากรอกเลขบิลใหม่ก่อนบันทึก');
    inp?.focus();
    return;
  }

  try {
    const res = await fetch('/return/' + id + '/new-bill', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json',
      },
      body: JSON.stringify({ new_bill_no: val }),
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      showToast('✕ ' + (data.message || 'เกิดข้อผิดพลาด'));
      return;
    }

    // อัพเดต UI ทันที → ล็อก
    const stCell = document.getElementById('stcell-' + id);
    if (stCell) {
      stCell.innerHTML = `
        <span class="st-badge st-pending">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" width="11" height="11">
            <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>
          </svg>
          Pending
        </span>
        <div class="nb-locked">
          <span class="nb-lock-ico">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="12" height="12">
              <path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="10"/>
            </svg>
          </span>
          <span class="nb-locked-val">${data.new_bill_no}</span>
        </div>`;
    }

    showToast('✓ บันทึกบิลใหม่ ' + data.new_bill_no + ' เรียบร้อย');

  } catch (e) {
    showToast('✕ เกิดข้อผิดพลาด กรุณาลองใหม่');
  }
}

function showToast(msg) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2800);
}
</script>
</body>
</html>