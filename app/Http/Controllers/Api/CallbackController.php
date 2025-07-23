<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CallbackController extends Controller
{
    public function callstatusBulk(Request $request)
    {
        $data = $request->input('data');

        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        $tableMap = [
            'SO' => ['table' => 'tblbill', 'column' => 'billid'],
            'PO' => ['table' => 'pobills', 'column' => 'po_id'],
            'TEMP' => ['table' => 'docbills', 'column' => 'doc_id'],
        ];

        $updatedCount = 0;
        $notFound = [];

        foreach ($data as $item) {
            $type = $item['type'] ?? null;
            $billId = $item['bill_id'] ?? null;
            $ng = $item['ng_value'] ?? null;

            if (!$type || !$billId || !isset($tableMap[$type])) {
                continue;
            }

            $table = $tableMap[$type]['table'];
            $column = $tableMap[$type]['column'];

            $updated = DB::table($table)
                ->where($column, $billId)
                ->update(['NG' => $ng]);

            if ($updated) {
                $updatedCount++;
            } else {
                $notFound[] = $billId;
            }
        }

        return response()->json([
            'status' => 'success',
            'updated' => $updatedCount,
            'not_found' => $notFound,
        ]);
    }
        public function callstatussuccess(Request $request)
    {
        $data = $request->input('data');

        if (!is_array($data)) {
            return response()->json(['error' => 'Invalid data format'], 400);
        }

        $tableMap = [
            'SO' => ['table' => 'tblbill', 'column' => 'billid'],
            'PO' => ['table' => 'pobills', 'column' => 'po_id'],
            'TEMP' => ['table' => 'docbills', 'column' => 'doc_id'],
        ];

        $updatedCount = 0;
        $notFound = [];

        foreach ($data as $item) {
            $type = $item['type'] ?? null;
            $billId = $item['bill_id'] ?? null;

            if (!$type || !$billId || !isset($tableMap[$type])) {
                continue;
            }

            $table = $tableMap[$type]['table'];
            $column = $tableMap[$type]['column'];

            $updated = DB::table($table)
                ->where($column, $billId)
                ->update(['statusdeli' => '1']);

            if ($updated) {
                $updatedCount++;
            } else {
                $notFound[] = $billId;
            }
        }

        return response()->json([
            'status' => 'success',
            'updated' => $updatedCount,
            'not_found' => $notFound,
        ]);
    }
}
