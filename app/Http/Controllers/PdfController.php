<?php
public function overwritePdf(Request $request)
{
    $request->validate([
        'pdffile' => 'required|mimes:pdf|max:10000',
        'billid' => 'required|string',
    ]);

    $file = $request->file('pdffile');
    $filename = $request->input('billid') . '.pdf';
    $path = 'public/template/' . $filename;

    if (Storage::exists($path)) {
        Storage::delete($path);
    }

    Storage::putFileAs('public/template', $file, $filename);

    return response()->json(['message' => "📄 ไฟล์ '$filename' ถูกแทนที่แล้ว"]);
}
