@auth
@php
    $__bUser = Auth::guard('web')->user();
    $__bName = $__bUser->name ?? $__bUser->username ?? ($__bUser->id_emp ?? 'ผู้ใช้งาน');
    $__bRole = $__bUser->role ?? '';
@endphp
<style>@media print { #global-user-badge { display:none !important; } }</style>
<div id="global-user-badge" style="
    position:fixed;top:12px;right:12px;z-index:2147483000;
    display:flex;align-items:center;gap:7px;
    background:#2853d5;color:#fff;
    font-family:'Noto Sans Thai','Segoe UI',Tahoma,Arial,sans-serif;
    font-size:13px;font-weight:600;line-height:1;
    padding:7px 14px;border-radius:16px;
    box-shadow:0 2px 8px rgba(0,0,0,.18);
    white-space:nowrap;pointer-events:none;user-select:none;">
    <span style="width:7px;height:7px;border-radius:50%;background:#22c55e;
        box-shadow:0 0 0 2px rgba(255,255,255,.35);display:inline-block;"></span>
    <span>{{ $__bName }}</span>
    @if($__bRole !== '')
        <span style="opacity:.7;font-weight:500;">({{ $__bRole }})</span>
    @endif
</div>
@endauth
