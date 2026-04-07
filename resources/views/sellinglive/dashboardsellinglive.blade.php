<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Selling Live — ระบบจัดการการขาย</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #0F6E56;
    --primary-light: #E1F5EE;
    --secondary: #185FA5;
    --bg: #F0F4F8;
    --surface: #FFFFFF;
    --text: #0F172A;
    --text-muted: #64748B;
    --border: #E2E8F0;
    --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
}

* { box-sizing: border-box; margin: 0; padding: 0; }
body {
    font-family: 'Sarabun', sans-serif;
    background-color: var(--bg);
    color: var(--text);
    line-height: 1.6;
}

.navbar {
    background: var(--surface);
    padding: 1rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: var(--shadow);
    position: sticky;
    top: 0;
    z-index: 10;
}

.brand {
    font-size: 1.25rem;
    font-weight: 800;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.header {
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
}

.header h1 {
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--surface);
    padding: 1.5rem;
    border-radius: 1rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
}

.stat-card .label {
    font-size: 0.875rem;
    color: var(--text-muted);
    font-weight: 600;
}

.stat-card .value {
    font-size: 1.5rem;
    font-weight: 800;
    margin-top: 0.5rem;
    color: var(--primary);
}

.table-container {
    background: var(--surface);
    border-radius: 1rem;
    box-shadow: var(--shadow);
    border: 1px solid var(--border);
    overflow: hidden;
}

.table-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h2 {
    font-size: 1.125rem;
    font-weight: 700;
}

table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
}

th {
    background: #F8FAFC;
    padding: 0.75rem 1.5rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border);
}

td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.875rem;
}

tr:hover {
    background-color: #F1F5F9;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-success { background: var(--primary-light); color: var(--primary); }

.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
    border: none;
    font-family: inherit;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: #085041;
}

@media (max-width: 768px) {
    .header { flex-direction: column; align-items: flex-start; gap: 1rem; }
}
</style>
</head>
<body>

<nav class="navbar">
    <div class="brand">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="3" y1="9" x2="21" y2="9"></line><line x1="9" y1="21" x2="9" y2="9"></line></svg>
        Selling Live System
    </div>
    <div class="user-info">
        <span style="font-weight: 600;">{{ Auth::user()->name ?? 'ผู้ใช้งาน' }}</span>
    </div>
</nav>

<div class="container">
    <div class="header">
        <div>
            <h1>Dashboard Selling Live</h1>
            <p style="color: var(--text-muted);">ภาพรวมการขายและสถานะใบมัดจำล่าสุด</p>
        </div>
        <a href="/create-deposit" class="btn btn-primary">+ สร้างใบมัดจำใหม่</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="label">ยอดขายวันนี้</div>
            <div class="value">฿ 125,400.00</div>
        </div>
        <div class="stat-card">
            <div class="label">ใบมัดจำที่รอดำเนินการ</div>
            <div class="value">12 รายการ</div>
        </div>
        <div class="stat-card">
            <div class="label">รายการที่สำเร็จแล้ว</div>
            <div class="value">45 รายการ</div>
        </div>
    </div>

    <div class="table-container">
        <div class="table-header">
            <h2>รายการล่าสุด</h2>
            <button class="btn" style="background: var(--bg); color: var(--text);">ดูทั้งหมด</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>เลขที่เอกสาร</th>
                    <th>ลูกค้า</th>
                    <th>วันที่</th>
                    <th>ยอดรวม</th>
                    <th>สถานะ</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <!-- ตัวอย่างข้อมูล -->
                <tr>
                    <td style="font-weight: 600;">DEP-2024001</td>
                    <td>บริษัท ตัวอย่าง จำกัด</td>
                    <td>07 เม.ย. 2024</td>
                    <td>฿ 5,000.00</td>
                    <td><span class="badge badge-success">สำเร็จ</span></td>
                    <td><a href="#" style="color: var(--secondary); font-weight: 600;">ดูรายละเอียด</a></td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">DEP-2024002</td>
                    <td>คุณสมชาย ใจดี</td>
                    <td>07 เม.ย. 2024</td>
                    <td>฿ 2,500.00</td>
                    <td><span class="badge badge-success">สำเร็จ</span></td>
                    <td><a href="#" style="color: var(--secondary); font-weight: 600;">ดูรายละเอียด</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
