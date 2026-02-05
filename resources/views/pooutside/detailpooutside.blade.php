<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="{{ asset('css/insertpo.blade.css') }}">
    <title>เช็ค PO ภายนอก</title>
<style>
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        padding: 10px 0;
    }

    .text-dark {
        font-size: 24px;
        color: #333333;
        margin: 0;
    }

    .btn-back {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 6px 8px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
    }

    .btn-back:hover {
        background-color: #0b46cf;
    }

    .status-complete {
        background-color: #d4edda;
        color: #155724;
        font-weight: bold;
    }

    .status-pending {
        background-color: #fff3cd;
        color: #856404;
        font-weight: bold;
    }

    .status-partial {
        background-color: #cce5ff;
        color: #004085;
        font-weight: bold;
    }

    .status-no-data {
        background-color: #f8d7da;
        color: #721c24;
        font-weight: bold;
    }
</style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-bar">
                <h2 class="text-dark">เช็ค PO ภายนอก</h2>
                <button onclick="history.back()" class="btn-back">ย้อนกลับ</button>
            </div>
            <div class="mb-3">
                <label class="form-label">เลขที่ PO :</label>
                <form id="poSearchForm">
                    <div style="display: flex; justify-content: space-between;">
                       <input type="text" class="form-control" id="po_number" name="po_number" style="width: 83%;" value="{{ $ponum ?? '' }}" required>
                        <button type="submit" class="btn-search" style="width: 14%; height: 30px; background-color:rgb(30, 62, 122); color:#fff;">ค้นหา</button>
                    </div>
                </form>
            </div>

            <form id="billForm">
                <input type="hidden" name="po_id" id="po_id" value="">
                <input type="hidden" name="status" id="status" value="0">
                <input type="hidden" id="store_id" name="store_id" readonly>
                
                <label>ชื่อร้านค้า :</label>
                <input type="text" id="store_name" name="store_name" readonly>

                <label>ที่อยู่ :</label>
                <input type="text" id="store_address" name="store_address" readonly>
                
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Date Invoice</th>
                            <th>รายการ</th>
                            <th>จำนวนที่สั่ง</th>
                            <th>จำนวนที่มาแล้ว</th>
                            <th>สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr> 
                            <td colspan="6" style="text-align: center; color: #999;">กรุณาค้นหา PO ก่อน</td>
                        </tr>
                    </tbody>
                </table>
            </form>
        </div>
    </div>

    <script>
        const poSearchForm = document.getElementById("poSearchForm");
        if (poSearchForm) {
            poSearchForm.addEventListener("submit", async function(event) {
                event.preventDefault();
                
                const poNumberInput = document.getElementById("po_number");
                if (!poNumberInput) {
                    alert("ไม่พบช่องกรอกเลขที่ PO");
                    return;
                }

                let poNumber = poNumberInput.value.trim();
                if (!poNumber) {
                    alert("กรุณากรอกเลขที่ PO");
                    return;
                }

                try {
                    let response = await fetch(`http://server_update:8000/api/getPODetail?PONum=${poNumber}`);

                    if (!response.ok) {
                        throw new Error("เกิดข้อผิดพลาดในการโหลดข้อมูล");
                    }

                    let data = await response.json();
                    console.log("API Response:", data);

                    if (!data || !data.DocuNo || !data.ms_podt || data.ms_podt.length === 0) {
                        alert("ไม่พบข้อมูลที่ตรงกับเลขที่ PO นี้");
                        return;
                    }

                    const storeIdInput = document.getElementById("store_id");
                    const storeNameInput = document.getElementById("store_name");
                    const storeAddressInput = document.getElementById("store_address");
                    
                    if (storeIdInput) storeIdInput.value = data.VendorCode || '';
                    if (storeNameInput) storeNameInput.value = data.VendorName || '';
                    
                    if (storeAddressInput) {
                        storeAddressInput.value = [
                            data.ContAddr1,
                            data.ContAddr2,
                            data.ContDistrict,
                            data.ContAmphur,
                            data.ContProvince,
                            data.ContPostCode
                        ].filter(Boolean).join(', ');
                    }
                    
                    let tbody = document.querySelector('table tbody');
                    if (tbody) {
                        tbody.innerHTML = '';
                        let itemCounter = 1;

                        for (const item of data.ms_podt) {
                            const itemId = `53-${String(itemCounter).padStart(4, '0')}`;
                            
                            let orderedQty = parseFloat(item.GoodQty2 || 0);
                            let completeFlag = item.CompleteFlag || 'N'; // CompleteFlag จาก API
                            
                            // ค้นหาข้อมูล Invoice จาก DB (ส่ง CompleteFlag ไปด้วย)
                            let invoiceData = await searchInvoiceFromDB(poNumber, item.GoodName, orderedQty, completeFlag);
                            
                            let invoiceNumber = invoiceData.invice || '';
                            let invoiceDate = invoiceData.date_invice || '';
                            let totalReceived = invoiceData.total_received || 0;
                            let hasData = invoiceData.has_data || false;
                            
                            let statusText = '';
                            let statusClass = '';
                            
                            // === Logic ใหม่ ===
                            if (!hasData) {
                                // 1. ไม่มีข้อมูลใน DB
                                statusText = 'ยังไม่มีข้อมูล';
                                statusClass = 'status-no-data';
                            } else {
                                // 2. มีข้อมูลใน DB แล้ว
                                if (completeFlag === 'Y') {
                                    // CompleteFlag = Y -> ตรวจสอบจำนวน
                                    if (totalReceived >= orderedQty) {
                                        statusText = `สินค้ามาครบแล้ว`;
                                        statusClass = 'status-complete';
                                    } else if (totalReceived > 0) {
                                        statusText = `มาแล้ว ${totalReceived} (ยังไม่ครบ)`;
                                        statusClass = 'status-partial';
                                    } else {
                                        // แปลกที่ CompleteFlag = Y แต่ quantity = 0
                                        statusText = 'กำลังจัดส่งรอ 10-15 วัน';
                                        statusClass = 'status-pending';
                                    }
                                } else {
                                    // CompleteFlag = N -> รอของเข้า
                                    statusText = 'กำลังจัดส่งรอ 10-15 วัน';
                                    statusClass = 'status-pending';
                                }
                            }

                            let invoiceDetails = '';
                            if (invoiceData.records && invoiceData.records.length > 0) {
                                invoiceDetails = invoiceData.records.map(r => 
                                    `${r.invoice} (${r.date}): ${r.quantity}`
                                ).join('<br>');
                            } else {
                                invoiceDetails = invoiceNumber;
                            }

                            let row = `
                                <tr>
                                    <td><div style="font-size: 11px;">${invoiceDetails || '-'}</div></td>
                                    <td><input type="text" class="form-control1" name="docu_date[]" value="${invoiceDate}" readonly></td>
                                    <td><input type="text" class="form-control1" name="item_name[]" value="${(item.GoodName || '').replace(/"/g, '&quot;').trim()}" readonly></td>
                                    <td><input type="text" class="form-control1 item_quantity" name="ordered_quantity[]" value="${orderedQty.toFixed(2)}" readonly></td>
                                    <td><input type="text" class="form-control1 item_quantity" name="received_quantity[]" value="${totalReceived.toFixed(2)}" readonly style="font-weight: bold;"></td>
                                    <td><input type="text" class="form-control1 ${statusClass}" name="status[]" value="${statusText}" readonly></td>
                                </tr>
                            `;
                            tbody.innerHTML += row;
                            itemCounter++;
                        }
                    }

                } catch (error) {
                    console.error('Error fetching data:', error);
                    alert('เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error.message);
                }
            });
        }

        async function searchInvoiceFromDB(poNumber, goodName, quantity, completeFlag) {
            try {
                console.log('Searching invoice for:', { poNumber, goodName, quantity, completeFlag });

                let response = await fetch('{{ route("pooutside.searchInvoice") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        po_number: poNumber,
                        good_name: goodName,
                        quantity: quantity,
                        complete_flag: completeFlag
                    })
                });

                let data = await response.json();
                console.log('Response data:', data);
                
                if (data.success) {
                    let formattedDate = data.date_invice ? formatDateFromDB(data.date_invice) : '';
                    
                    return {
                        invice: data.invice || '',
                        date_invice: formattedDate,
                        total_received: data.total_received || 0,
                        is_complete: data.is_complete || false,
                        has_data: data.has_data || false,
                        records: data.records || []
                    };
                } else {
                    return {
                        invice: '',
                        date_invice: '',
                        total_received: 0,
                        is_complete: false,
                        has_data: false,
                        records: []
                    };
                }
            } catch (error) {
                console.error('Error searching invoice:', error);
                return {
                    invice: '',
                    date_invice: '',
                    total_received: 0,
                    is_complete: false,
                    has_data: false,
                    records: []
                };
            }
        }

        function formatDateFromDB(dateString) {
            if (!dateString) return '';
            if (dateString.includes('/')) return dateString;
            
            let date = new Date(dateString);
            let day = date.getDate().toString().padStart(2, '0');
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }

        function formatDate(dateString) {
            if (!dateString) return '';
            let date = new Date(dateString);
            let day = date.getDate().toString().padStart(2, '0');
            let month = (date.getMonth() + 1).toString().padStart(2, '0');
            let year = date.getFullYear();
            return `${day}/${month}/${year}`;
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const poNum = '{{ $ponum ?? '' }}';
        if (poNum) {
            document.getElementById('poSearchForm').dispatchEvent(new Event('submit'));
        }
    });
</script>
</body>
</html>