<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ใบรับเงินมัดจำ {{ $deposit_bill_id }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:#ccc;font-size:13px;color:#111;line-height:1.5}

.toolbar{position:sticky;top:0;z-index:100;background:#fff;border-bottom:1px solid #ccc;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 1px 3px rgba(0,0,0,.1)}
.tb-title{font-size:13px;font-weight:700;color:#1a3a6b}
.tb-sub{font-size:11px;color:#888}
.tb-actions{display:flex;gap:8px}
.btn{padding:7px 16px;border:1px solid #bbb;background:#fff;color:#444;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
.btn:hover{background:#f5f5f5}
.btn-primary{background:#1a3a6b;color:#fff;border-color:#1a3a6b}
.btn-primary:hover{background:#14305a}

/* ===== A4 Page ===== */
.page-wrap{max-width:794px;margin:24px auto;padding-bottom:40px}
.page{background:#fff;padding:22px 28px;border:1px solid #999;box-shadow:0 2px 12px rgba(0,0,0,.2);position:relative;font-size:12px}

/* ---- Header ---- */
.header{display:grid;grid-template-columns:1fr auto auto;gap:12px;align-items:start;border-bottom:2px solid #111;padding-bottom:8px;margin-bottom:6px}

.co-name{font-size:14.5px;font-weight:800;color:#1a3a6b;line-height:1.2}
.co-name-en{font-size:10px;color:#555;font-weight:600;margin-bottom:3px}
.co-info{font-size:10.5px;color:#333;line-height:1.7}

.doc-title-box{border:1.5px solid #111;padding:5px 14px;text-align:center;white-space:nowrap;align-self:center}
.doc-title-box .main{font-size:13px;font-weight:800;color:#1a3a6b}
.doc-title-box .sub{font-size:10px;color:#444;margin-top:1px}

.doc-no-box{text-align:right;font-size:11px;min-width:120px}
.doc-no-box .no{font-size:14px;font-weight:800;color:#c00;letter-spacing:.5px}
.doc-no-box .row{display:flex;justify-content:flex-end;gap:4px;margin-top:2px}
.doc-no-box .lbl{color:#777}
.doc-no-box .val{font-weight:600}

/* ---- Stamp ---- */
.stamp{position:absolute;top:70px;right:60px;border:3px solid #16a34a;color:#16a34a;padding:5px 16px;font-size:15px;font-weight:800;letter-spacing:.1em;transform:rotate(-12deg);opacity:.85;pointer-events:none}
.stamp.pending{border-color:#d97706;color:#d97706}

/* ---- Branch row ---- */
.branch-row{font-size:10.5px;color:#555;margin-bottom:6px;padding-bottom:5px;border-bottom:1px solid #ddd}

/* ---- Customer grid ---- */
.cus-grid{display:grid;grid-template-columns:1fr 1fr;border:1px solid #aaa;margin-bottom:8px}
.cus-cell{padding:5px 9px;border-right:1px solid #aaa;border-bottom:1px solid #aaa;min-width:0}
.cus-cell:nth-child(2n){border-right:none}
.cus-cell.full{grid-column:1/3;border-right:none}
.cus-cell:nth-last-child(-n+2):not(.full){border-bottom:none}
.cus-cell.full:last-child{border-bottom:none}
.cus-lbl{font-size:9.5px;color:#999;text-transform:uppercase;letter-spacing:.04em;margin-bottom:1px}
.cus-val{font-size:12px;font-weight:600;color:#111;word-break:break-word;white-space:normal}
.cus-val.name{font-size:13px;font-weight:800}

/* ---- Main table ---- */
table.main{width:100%;border-collapse:collapse;font-size:12px;margin-bottom:0;table-layout:fixed}
table.main col.col-no{width:62px}
table.main col.col-desc{width:auto}
table.main col.col-amt{width:130px}
table.main thead tr{background:#1a3a6b;color:#fff}
table.main th{padding:6px 10px;font-size:11px;font-weight:700;border:1px solid #1a3a6b;text-align:left;white-space:nowrap}
table.main th.r{text-align:right}
table.main th.c{text-align:center}
table.main td{padding:7px 10px;border:1px solid #ccc;vertical-align:top;word-break:break-word;white-space:normal;line-height:1.6}
table.main td.r{text-align:right;font-variant-numeric:tabular-nums;white-space:nowrap}
table.main td.c{text-align:center;white-space:nowrap}
table.main tbody tr:nth-child(even){background:#f8f9fb}
.empty td{height:22px;border-color:#e0e0e0}

/* ---- Bottom ---- */
.bottom{display:flex;border:1px solid #aaa;border-top:none}
.bot-left{flex:1;padding:9px 11px;border-right:1px solid #aaa;font-size:11.5px}
.bot-right{width:220px;display:flex;flex-direction:column}

.note-lbl{font-size:9.5px;color:#999;text-transform:uppercase;letter-spacing:.04em;margin-bottom:3px}
.note-txt{color:#444;line-height:1.55;margin-bottom:5px}
.warn-txt{font-size:11px;color:#555;margin-bottom:6px}
.amount-words{background:#eef3fa;border:1px solid #b5c9e8;padding:6px 10px;font-size:11.5px}
.amount-words .lbl{font-size:9.5px;color:#888;display:block;margin-bottom:2px}
.amount-words .val{font-weight:700;color:#1a3a6b}

/* payment method */
.pay-method{display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid #aaa}
.pm{padding:5px 8px;border-right:1px solid #aaa;font-size:11px}
.pm:last-child{border-right:none}
.pm .lbl{font-size:9px;color:#999;text-transform:uppercase;margin-bottom:2px}
.pm .val{font-weight:600;min-height:16px}

/* check info */
.check-row{display:grid;grid-template-columns:1fr 1fr 1fr;border-bottom:1px solid #aaa}
.ch{padding:4px 8px;border-right:1px solid #aaa;font-size:11px;color:#555}
.ch:last-child{border-right:none}
.ch .lbl{font-size:9px;color:#999;margin-bottom:2px}
.ch-full{grid-column:1/4;border-right:none}

/* summary */
table.summ{width:100%;border-collapse:collapse}
table.summ td{padding:5px 10px;font-size:12px;border-bottom:1px solid #e5e5e5}
table.summ td.lbl{color:#555}
table.summ td.val{text-align:right;font-weight:600;font-variant-numeric:tabular-nums}
table.summ tr.total td{background:#1a3a6b;color:#fff;font-weight:800;font-size:13px;border-bottom:none}
table.summ tr.total td.val{color:#fff}

/* signatures */
.sig-row{display:grid;grid-template-columns:1fr 1fr 1fr;border:1px solid #aaa;border-top:none}
.sig{padding:10px 10px 7px;border-right:1px solid #aaa;text-align:center}
.sig:last-child{border-right:none}
.sig-line{border-bottom:1px dotted #aaa;height:46px;margin-bottom:5px}
.sig-lbl{font-size:11px;color:#444;font-weight:600}
.sig-date{font-size:10px;color:#999;margin-top:2px}

.footer{margin-top:6px;text-align:center;font-size:10px;color:#aaa;border-top:1px solid #eee;padding-top:5px}

@media print{
  @page{size:A4;margin:10mm 12mm}
  body{background:#fff}
  .toolbar{display:none!important}
  .page-wrap{margin:0;max-width:100%}
  .page{box-shadow:none;border:none;padding:0}
  .stamp{right:30px;top:60px}
}
</style>
</head>
<body>

<div class="toolbar">
  <div>
    <div class="tb-title">ใบรับเงินมัดจำ {{ $deposit_bill_id }}</div>
    <div class="tb-sub">SO: {{ $header->so_id }} · {{ $header->customer_name }}</div>
  </div>
  <div class="tb-actions">
    <button class="btn" onclick="history.back()">← ย้อนกลับ</button>
    <button class="btn btn-primary" onclick="window.print()">🖨 พิมพ์ / บันทึก PDF</button>
  </div>
</div>

<div class="page-wrap">
<div class="page">

  {{-- Stamp --}}
  @if($header->status === 'ยืนยัน')
    <div class="stamp">ยืนยันแล้ว</div>
  @else
    <div class="stamp pending">รอยืนยัน</div>
  @endif

  {{-- Header --}}
  <div class="header">
    <div>
      <div class="co-name">บริษัท ทริปเปิ้ล อี เทรดดิ้ง จำกัด</div>
      <div class="co-name-en">TRIPLE E TRADING CO., LTD.</div>
      <div class="co-info">
        สำนักงานใหญ่: เลขที่ 39/7 ถนนวุฒากาส แขวงตลาดพลู เขตธนบุรี กรุงเทพฯ 10600<br>
        โทร. 02-4727341-40 &nbsp;|&nbsp; โทรสาร 02-4727349-50<br>
        เลขประจำตัวผู้เสียภาษีอากร <strong>0105549013885</strong>
      </div>
    </div>

    <div class="doc-title-box">
      <div class="main">ต้นฉบับใบรับเงินมัดจำ</div>
      <div class="sub">ใบกำกับภาษี / ใบเสร็จรับเงิน</div>
    </div>

    <div class="doc-no-box">
      <div class="no">{{ $deposit_bill_id }}</div>
      <div class="row">
        <span class="lbl">วันที่:</span>
        <span class="val">
          @php
            $issueDate = $header->date_dep
              ? \Carbon\Carbon::parse($header->date_dep)
              : \Carbon\Carbon::parse($header->time);
          @endphp
          {{ $issueDate->format('d/m/') }}{{ $issueDate->year + 543 }}
        </span>
      </div>
      <div class="row"><span class="lbl">หน้า:</span><span class="val">1 / 1</span></div>
    </div>
  </div>

  {{-- Branch --}}
  <div class="branch-row">
    สาขาที่ออกใบกำกับภาษี: <strong>สำนักงานใหญ่</strong>
  </div>

  {{-- Customer --}}
  <div class="cus-grid">
    <div class="cus-cell">
      <div class="cus-lbl">นามผู้ซื้อ</div>
      <div class="cus-val name">{{ $header->customer_name ?: '—' }}</div>
    </div>
    <div class="cus-cell">
      <div class="cus-lbl">รหัสลูกค้า</div>
      <div class="cus-val">{{ $header->customer_id ?: '—' }}</div>
    </div>
    <div class="cus-cell full">
      <div class="cus-lbl">ที่อยู่</div>
      <div class="cus-val">{{ $header->customer_address ?: '—' }}</div>
    </div>
    <div class="cus-cell">
      <div class="cus-lbl">โทร.</div>
      <div class="cus-val">{{ $header->customer_tel ?: '—' }}</div>
    </div>
    <div class="cus-cell">
      <div class="cus-lbl">โทรสาร</div>
      <div class="cus-val">{{ $header->customer_fax ?: '—' }}</div>
    </div>
    <div class="cus-cell">
      <div class="cus-lbl">เลขประจำตัวผู้เสียภาษี</div>
      <div class="cus-val">{{ $header->customer_tax_id ?: '—' }}</div>
    </div>
    <div class="cus-cell">
      <div class="cus-lbl">สาขา</div>
      <div class="cus-val">{{ $header->customer_branch ?: 'สำนักงานใหญ่' }}</div>
    </div>
  </div>

  {{-- Items table --}}
  <table class="main">
    <colgroup>
      <col class="col-no">
      <col class="col-desc">
      <col class="col-amt">
    </colgroup>
    <thead>
      <tr>
        <th class="c">ลำดับที่</th>
        <th>รายการ</th>
        <th class="r">จำนวนเงิน</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $i => $item)
      <tr>
        <td class="c">{{ $i + 1 }}</td>
        <td>{{ $item->description }}</td>
        <td class="r">{{ number_format((float)$item->dep_price, 2) }}</td>
      </tr>
      @endforeach
      {{-- empty filler rows --}}
      @for($e = count($items); $e < 7; $e++)
      <tr class="empty"><td></td><td></td><td></td></tr>
      @endfor
    </tbody>
  </table>

  {{-- Bottom section --}}
  <div class="bottom">
    <div class="bot-left">
      <div class="note-lbl">หมายเหตุ</div>
      <div class="note-txt">ในกรณีที่ชำระเงินเป็นเช็ค ใบเสร็จฉบับนี้จะสมบูรณ์ก็ต่อเมื่อบริษัทฯ เรียกเก็บเงินจากธนาคารได้แล้ว</div>
      <div class="warn-txt">โปรดสั่งจ่ายเช็คในนามบริษัทฯ เท่านั้น</div>
      <div class="amount-words">
        <span class="lbl">จำนวนเงิน (ตัวอักษร):</span>
        <span class="val">
          @php
            function bahtText($amount) {
              $n1=['ศูนย์','หนึ่ง','สอง','สาม','สี่','ห้า','หก','เจ็ด','แปด','เก้า'];
              $n2=['','สิบ','ร้อย','พัน','หมื่น','แสน','ล้าน'];
              [$int,$dec]=explode('.',number_format($amount,2,'.',''));
              $b='';$s='';$il=strlen($int);
              for($i=0;$i<$il;$i++){$d=(int)$int[$i];$p=$il-$i-1;if(!$d)continue;
                if($p==0&&$d==1&&$il>1){$b.='เอ็ด';}
                elseif($p==1&&$d==2){$b.='ยี่สิบ';}
                elseif($p==1&&$d==1){$b.='สิบ';}
                else{$b.=$n1[$d].$n2[$p];}}
              $b.='บาท';$dn=(int)$dec;
              if(!$dn){$s='ถ้วน';}else{$dl=strlen($dec);
                for($i=0;$i<$dl;$i++){$d=(int)$dec[$i];$p=$dl-$i-1;if(!$d)continue;
                  if($p==0&&$d==1&&$dl>1){$s.='เอ็ด';}
                  elseif($p==1&&$d==2){$s.='ยี่สิบ';}
                  elseif($p==1&&$d==1){$s.='สิบ';}
                  else{$s.=$n1[$d].$n2[$p];}}$s.='สตางค์';}
              return $b.$s;}
          @endphp
          ( {{ bahtText($totalDeposit) }} )
        </span>
      </div>
    </div>

    <div class="bot-right">
      {{-- Payment method --}}
      <div class="pay-method">
        <div class="pm"><div class="lbl">ชำระโดย</div><div class="val">☐ เงินสด</div></div>
        <div class="pm"><div class="lbl">&nbsp;</div><div class="val">☐ เช็ค</div></div>
        <div class="pm"><div class="lbl">&nbsp;</div><div class="val">☐ เงินโอน</div></div>
      </div>
      {{-- Check info --}}
      <div class="check-row">
        <div class="ch"><div class="lbl">ในนาม</div><div style="min-height:16px"></div></div>
        <div class="ch"><div class="lbl">ธนาคาร</div><div style="min-height:16px"></div></div>
        <div class="ch"><div class="lbl">สาขา</div><div style="min-height:16px"></div></div>
      </div>
      <div class="check-row">
        <div class="ch ch-full"><div class="lbl">เลขที่เช็ค / ลงวันที่</div><div style="min-height:16px"></div></div>
      </div>
      {{-- Summary --}}
      <table class="summ">
        <tr>
          <td class="lbl">หมายเหตุ รวมเงิน</td>
          <td class="val">{{ number_format($totalDeposit, 2) }}</td>
        </tr>
        <tr>
          <td class="lbl">ภาษีมูลค่าเพิ่ม 7%</td>
          <td class="val">{{ number_format($totalDeposit * 0.07, 2) }}</td>
        </tr>
        <tr class="total">
          <td class="lbl">จำนวนเงินทั้งสิ้น</td>
          <td class="val">{{ number_format($totalDeposit * 1.07, 2) }}</td>
        </tr>
      </table>
    </div>
  </div>

  {{-- Signatures --}}
  <div class="sig-row">
    <div class="sig">
      <div class="sig-line"></div>
      <div class="sig-lbl">จัดเตรียมโดย</div>
      <div class="sig-date">วันที่ _____ / _____ / _______</div>
    </div>
    <div class="sig">
      <div class="sig-line"></div>
      <div class="sig-lbl">ตรวจสอบโดย</div>
      <div class="sig-date">วันที่ _____ / _____ / _______</div>
    </div>
    <div class="sig">
      <div class="sig-line"></div>
      <div class="sig-lbl">ผู้มีอำนาจลงนาม (สำนักงานใหญ่)</div>
      <div class="sig-date">วันที่ _____ / _____ / _______</div>
    </div>
  </div>

  <div class="footer">
    ได้รับชำระเงินไว้ถูกต้องแล้วด้วยความขอบคุณ &nbsp;·&nbsp;
    พิมพ์เมื่อ {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }} น.
  </div>

</div>
</div>

</body>
</html>