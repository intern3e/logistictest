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
          <span class="grp-driver">· คนขับ: {{ $g['driver'] }}</span>
          <span class="grp-cnt">{{ $g['total'] }} บิล</span>
        </div>
        <div class="grp-chips">
          @foreach ($g['bills'] as $no)
            <span class="chip bill">{{ $no }}</span>
          @endforeach
          @foreach ($g['docs'] as $no)
            <span class="chip doc">{{ $no }}</span>
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</div>
