<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการเซอร์วิส - Modern Dashboard & Charts</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&family=IBM+Plex+Mono:wght@400;500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        
        :root{--navy:#1a3a6b;--navy-light:#243258;--navy-dark:#122956;--accent:#4f8ef7;--accent2:#38c98a;--accent3:#f5a623;--accent4:#e85d5d;--bg:#f0f2f7;--surface:#fff;--surface2:#f8f9fc;--border:#e2e6f0;--text:#1a2744;--text2:#6b7a99;--text3:#9aa3bc;--shadow:0 2px 12px rgba(26,39,68,.08);--shadow-md:0 4px 24px rgba(26,39,68,.12);--radius:12px;--radius-sm:8px}
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Sarabun',sans-serif;background:var(--bg);color:var(--text);min-height:100vh}
.navbar{background:var(--navy);padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:100;box-shadow:0 2px 16px rgba(0,0,0,.18)}
.navbar-brand{display:flex;align-items:center;gap:10px;color:#fff;font-weight:600;font-size:16px}
.navbar-brand .icon{width:36px;height:36px;background:rgba(255,255,255,.12);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:18px}
.navbar-brand .sub{font-size:11px;font-weight:300;opacity:.65;letter-spacing:1px}
.nav-date{color:rgba(255,255,255,.75);font-size:13px}
.nav-user{display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.1);border-radius:20px;padding:5px 14px 5px 6px}
.nav-avatar{width:28px;height:28px;background:var(--accent);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:600;font-size:12px;color:#fff}
.nav-user span{color:#fff;font-size:13px;font-weight:500}
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #0b1c6f;
            --primary-d: #1e40af;
            --accent: #f59e0b;
            --bg: #f8fafc;
            --white: #ffffff;
            --text: #0f172a;
            --muted: #ffffff;
            --border: #e2e8f0;
            --radius-lg: 12px;
            --radius-md: 8px;
            --shadow: 0 4px 6px -1px rgba(31, 72, 255, 0.1);
            --font: 'Noto Sans Thai', sans-serif;
            --mono: 'IBM Plex Mono', monospace;
        }

        body { font-family: var(--font); background: var(--bg); color: var(--text); min-height: 100vh; }

        .topbar {
            background: var(--white); height: 70px; display: flex; align-items: center;
            justify-content: space-between; padding: 0 40px; position: sticky; top: 0;
            z-index: 100; border-bottom: 1px solid var(--border);
        }
        .brand { display: flex; align-items: center; gap: 12px; }
        .brand-logo { width: 40px; height: 40px; background: var(--primary); border-radius: var(--radius-md); display: flex; align-items: center; justify-content: center; color: #ffffff; }
        .brand-name { font-size: 18px; font-weight: 800; color: var(--primary); }

        main { padding: 30px 40px; max-width: 1400px; margin: 0 auto; width: 100%; }

        /* Dashboard & Charts Grid */
        .dashboard-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px; }
        .chart-grid { display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 30px; }

        .stat-card, .chart-card {
            background: var(--white); padding: 24px; border-radius: var(--radius-lg);
            border: 1px solid var(--border); box-shadow: var(--shadow);
        }

        .stat-card { display: flex; flex-direction: column; gap: 8px; }
        .stat-card .label { font-size: 13px; font-weight: 600; color: var(--muted); text-transform: uppercase; }
        .stat-card .value { font-size: 28px; font-weight: 800; color: var(--text); font-family: var(--mono); }
        .stat-card.primary { border-left: 4px solid var(--primary); }
        .stat-card.accent { border-left: 4px solid var(--accent); }

        .chart-card h3 { font-size: 14px; margin-bottom: 15px; color: var(--muted); }

        .table-container { background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); box-shadow: var(--shadow); overflow: hidden; }
        .table-header { padding: 20px 24px; display: flex; justify-content: space-between; align-items: center; background: #fff; border-bottom: 1px solid var(--border); }
        
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #171999; padding: 14px 24px; text-align: left; font-size: 12px; font-weight: 700; color: var(--muted); text-transform: uppercase; }
        tbody td { padding: 16px 24px; border-bottom: 1px solid #f8fafc; font-size: 14px; }

        .type-badge { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; }
        .type-check { background: #dcfce7; color: #166534; }
        .type-fix { background: #fef9c3; color: #854d0e; }
        .type-urgent { background: #e3e2fe; color: #991b1b; }

        .btn-add { background: var(--primary); color: white; padding: 10px 20px; border-radius: var(--radius-md); border: none; font-weight: 700; cursor: pointer; }
        .search-box { padding: 8px 16px; border: 1px solid var(--border); border-radius: var(--radius-md); width: 250px; }

        /* Modal */
        .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 1000; backdrop-filter: blur(4px); }
        .modal-overlay.show { display: flex; }
        .modal { background: white; width: 450px; border-radius: var(--radius-lg); overflow: hidden; }
        .modal-body { padding: 20px; display: flex; flex-direction: column; gap: 12px; }
        .modal-body input, .modal-body select, .modal-body textarea { padding: 10px; border: 1px solid var(--border); border-radius: var(--radius-md); }
    </style>
</head>
<body>
    <nav class="navbar">
  <div class="navbar-brand">
    <div class="icon">⛽</div>
    <div><div>ระบบจัดการเซอร์วิส</div><div class="sub">SERVICE MANAGEMENT SYSTEM</div></div>
  </div>
  <div style="display:flex;align-items:center;gap:16px">
    <div class="nav-date" id="navDate"></div>
    <div class="nav-user"><div class="nav-avatar">A</div><span>Admin</span></div>
  </div>
</nav>


<header class="topbar">
    <div class="brand">
        <div class="brand-logo">⚙️</div>
        <div class="brand-name">SERVICE HUB</div>
    </div>
    <div id="current-time" style="font-size: 14px; color: var(--muted);"></div>
</header>

<main>
    <div class="dashboard-grid">
        <div class="stat-card primary">
            <h3>จำนวนงานทั้งหมด</h3>
            <span class="label">จำนวนงานทั้งหมด</span>
            <span class="value" id="stat-total">0</span>
        </div>
        <div class="stat-card accent">
            <h3>ค่าใช้จ่ายรวม</h3>
            <span class="label">ค่าใช้จ่ายรวม</span>
            <span class="value" id="stat-cost">0</span>
        </div>
        <div class="stat-card" style="border-left: 4px solid #b91010;">
            <h3>เฉลี่ยต่อรายการ</h3>
            <span class="label">เฉลี่ยต่อรายการ</span>
            <span class="value" id="stat-avg">0</span>
        </div>
    </div>

    <div class="chart-grid">
        <div class="chart-card">
            <h3>สัดส่วนประเภทงาน</h3>
            <canvas id="typeChart"></canvas>
        </div>
        <div class="chart-card">
            <h3>แนวโน้มค่าใช้จ่าย</h3>
            <canvas id="costChart"></canvas>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>ประวัติการรับบริการ</h2>
            <div style="display: flex; gap: 15px;">
                <input type="text" class="search-box" placeholder="ค้นหาทะเบียน..." oninput="doSearch(this.value)">
                <button class="btn-add" onclick="openModal()">+ เพิ่มข้อมูล</button>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>วันที่</th>
                    <th>ทะเบียน</th>
                    <th>ประเภท</th>
                    <th>ค่าใช้จ่าย</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
</main>

<div class="modal-overlay" id="serviceModal">
    <div class="modal">
        <div class="modal-body">
            <h2 style="margin-bottom: 10px;">เพิ่มรายการใหม่</h2>
            <input type="date" id="inputDate">
            <select id="inputName">
                <option value="">เลือกทะเบียนรถ</option>
                <option value="1 ฉผ 1276">1 ฉผ 1276</option>
                <option value="2 ฉธ 1620">2 ฉธ 1620</option>
                <option value="City 8กค6309">City 8กค6309</option>
            </select>
            <select id="inputType">
                <option value="check">เช็คระยะ</option>
                <option value="fix">ซ่อมบำรุง</option>
                <option value="urgent">ฉุกเฉิน</option>
            </select>
            <input type="number" id="inputAmount" placeholder="ค่าใช้จ่าย (บาท)">
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button onclick="closeModal()" style="background:none; border:none; cursor:pointer;">ยกเลิก</button>
                <button class="btn-add" onclick="submitData()">บันทึก</button>
            </div>
        </div>
    </div>
</div>

<script>
    let records = [];
    let query = "";
    let typeChart, costChart;

    // Initialize Charts
    function initCharts() {
        const ctxType = document.getElementById('typeChart').getContext('2d');
        const ctxCost = document.getElementById('costChart').getContext('2d');

        typeChart = new Chart(ctxType, {
            type: 'doughnut',
            data: {
                labels: ['เช็คระยะ', 'ซ่อมบำรุง', 'ฉุกเฉิน'],
                datasets: [{
                    data: [0, 0, 0],
                    backgroundColor: ['#2563eb', '#f59e0b', '#ef4444'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } }, cutout: '70%' }
        });

        costChart = new Chart(ctxCost, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'ค่าใช้จ่าย',
                    data: [],
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { 
                scales: { y: { beginAtZero: true } },
                plugins: { legend: { display: false } }
            }
        });
    }

    function updateCharts() {
        // Update Type Chart
        const counts = { check: 0, fix: 0, urgent: 0 };
        records.forEach(r => counts[r.type]++);
        typeChart.data.datasets[0].data = [counts.check, counts.fix, counts.urgent];
        typeChart.update();

        // Update Cost Chart (Sort by date)
        const sorted = [...records].sort((a, b) => new Date(a.date) - new Date(b.date));
        costChart.data.labels = sorted.map(r => r.date);
        costChart.data.datasets[0].data = sorted.map(r => r.amount);
        costChart.update();
    }

    function render() {
        const filtered = query ? records.filter(r => r.name.includes(query)) : records;
        const tbody = document.getElementById('tbody');
        
        tbody.innerHTML = filtered.length ? filtered.map((r, i) => `
            <tr>
                <td style="font-family: var(--mono);">${r.date}</td>
                <td style="font-weight: 700;">${r.name}</td>
                <td><span class="type-badge type-${r.type}">${r.type === 'check' ? 'เช็คระยะ' : r.type === 'fix' ? 'ซ่อมบำรุง' : 'ฉุกเฉิน'}</span></td>
                <td style="font-family: var(--mono); font-weight: 700; color: var(--primary);">฿${Number(r.amount).toLocaleString()}</td>
                <td><button onclick="del(${i})" style="color: #ef4444; border: none; background: none; cursor: pointer;">ลบ</button></td>
            </tr>
        `).join('') : '<tr><td colspan="5" style="text-align: center; padding: 40px; color: var(--muted);">ไม่มีข้อมูล</td></tr>';

        // Update Stats
        const totalCost = records.reduce((sum, r) => sum + Number(r.amount), 0);
        document.getElementById('stat-total').innerText = records.length;
        document.getElementById('stat-cost').innerText = totalCost.toLocaleString();
        document.getElementById('stat-avg').innerText = records.length ? Math.round(totalCost/records.length).toLocaleString() : 0;

        updateCharts();
    }

    function submitData() {
        const data = {
            date: document.getElementById('inputDate').value,
            name: document.getElementById('inputName').value,
            type: document.getElementById('inputType').value,
            amount: document.getElementById('inputAmount').value,
        };
        if (!data.date || !data.name || !data.amount) return alert('กรุณากรอกข้อมูลให้ครบ');
        records.push(data);
        render();
        closeModal();
        // Clear inputs
        document.getElementById('inputAmount').value = '';
    }

    function openModal() { document.getElementById('serviceModal').classList.add('show'); }
    function closeModal() { document.getElementById('serviceModal').classList.remove('show'); }
    function doSearch(val) { query = val; render(); }
    function del(i) { if(confirm('ลบรายการนี้?')) { records.splice(i, 1); render(); } }

    setInterval(() => { document.getElementById('current-time').innerText = new Date().toLocaleString('th-TH'); }, 1000);

    // Start
    initCharts();
    render();
</script>
</body>
</html>