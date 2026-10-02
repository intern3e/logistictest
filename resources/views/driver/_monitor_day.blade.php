<div class="day">
  <div class="day-head">
    <span class="day-date">{{ $d['date_thai'] }}</span>
    <span class="day-total">ค้าง {{ $d['total'] }} บิล · ส่งของ {{ $d['bill_count'] }} · ชั่วคราว {{ $d['doc_count'] }}</span>
  </div>
  <div class="day-body">
    @foreach ($d['groups'] as $g)
      <div class="grp-row">
        <div class="grp-label">
          <span class="grp-transport">{{ $g['transport'] }}</span>
          @if (!empty($g['id_transport']))
            <span class="grp-idt">· รหัสขนส่ง: {{ $g['id_transport'] }}</span>
          @endif
          <span class="grp-driver">· คนขับ: {{ $g['driver'] }}</span>
          <span class="grp-cnt">{{ $g['total'] }} บิล</span>
        </div>

        @if (count($g['bills']))
          <div class="kind-label tag-bill">บิลส่งของ ({{ $g['bill_count'] }})</div>
          <div class="bill-list">
            @foreach ($g['bills'] as $b)
              <div class="bill-item bill">
                <div class="bi-top">
                  <span class="bi-no">{{ $b['no'] }}</span>
                  <span class="bi-cust">{{ $b['customer_name'] ?: '-' }}<span class="bi-cid">{{ $b['customer_id'] ? ' ('.$b['customer_id'].')' : '' }}</span></span>
                </div>
                <div class="bi-meta">
                  <span>ผู้เปิด: <b>{{ $b['opener'] ?: '-' }}</b></span>
                  <span>จ่ายงาน: <b>{{ $b['name_pick'] ?: '-' }}</b>{{ $b['time_pick'] ? ' · '.$b['time_pick'] : '' }}</span>
                </div>
              </div>
            @endforeach
          </div>
        @endif

        @if (count($g['docs']))
          <div class="kind-label tag-doc">บิลชั่วคราว ({{ $g['doc_count'] }})</div>
          <div class="bill-list">
            @foreach ($g['docs'] as $b)
              <div class="bill-item doc">
                <div class="bi-top">
                  <span class="bi-no">{{ $b['no'] }}</span>
                  <span class="bi-cust">{{ $b['customer_name'] ?: '-' }}<span class="bi-cid">{{ $b['customer_id'] ? ' ('.$b['customer_id'].')' : '' }}</span></span>
                  <button type="button" class="bi-items-btn" onclick="showDocItems('{{ $b['doc_id'] }}')">ดูสินค้า</button>
                </div>
                <div class="bi-meta">
                  <span>ผู้เปิด: <b>{{ $b['opener'] ?: '-' }}</b></span>
                  <span>จ่ายงาน: <b>{{ $b['name_pick'] ?: '-' }}</b>{{ $b['time_pick'] ? ' · '.$b['time_pick'] : '' }}</span>
                </div>
                @if (!empty($b['notes']))
                  <div class="bi-note">หมายเหตุ: {{ $b['notes'] }}</div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </div>
    @endforeach
  </div>
</div>
