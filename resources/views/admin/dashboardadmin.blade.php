<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header {
            background-color: #f1f1f1;
            padding: 15px;
        }
        .search-button {
            background-color: #ffa951;
            color: white;
        }
        .search-button:hover {
            background-color: #e89542;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <div class="header d-flex justify-content-between align-items-center">
        <h5>ระบบจัดเตรียมสินค้า</h5>
        <div>
            <span class="me-3">ผู้ใช้: {{ session('id_admin', 'Guest') }}</span>
            <form action="{{ route('logoutadmin') }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-danger">ออกจากระบบ</button>
            </form>
        </div>
    </div>
        <!-- Filter Section -->
        <form method="GET" action="{{ route('admin.dashboardadmin') }}" class="row mt-3 g-3">
 
            <div class="col-md-4">
                <label for="date" class="form-label">วันที่</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive mt-4">
            <table class="table table-bordered table-striped text-center">
                <thead class="table-light">
                    <tr>
                        <th>สถานะ</th>
                        <th>รหัสลูกค้า</th>
                        <th>ที่อยู่</th>
                        <th>วันที่</th>
                        <th>ข้อมูลสินค้า</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- @forelse ($bills as $bill)
                        <tr>
                            <td>{{ $bill->idcustomer }}</td>
                            <td>{{ $bill->address }}</td>
                            <td>{{ $bill->date }}</td>
                            <td><a href="{{ route('sale.insertdata', $bill->id) }}" class="text-primary">เพิ่มเติม</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">ไม่พบข้อมูล</td>
                        </tr>
                    @endforelse --}}
                </tbody>
                
            </table>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
