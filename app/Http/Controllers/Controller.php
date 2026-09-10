<?php

namespace App\Http\Controllers;

use App\Models\SsoTicket;
use App\Models\UserAuth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * บังคับให้ต้อง login ก่อนใช้งานหน้าเว็บ
     *
     * - ถ้ามี ticket (SSO จากระบบ 3e) จะ login ให้อัตโนมัติ
     * - ถ้ายังไม่ได้ login → เด้งไปหน้า login เสมอ (ไม่มี abort 403)
     *
     * @return UserAuth ผู้ใช้ที่ login อยู่
     */
    protected function requireLogin(?Request $request = null, string $logTag = 'page'): UserAuth
    {
        $request = $request ?: request();

        $ticket = $request->input('ticket');

        if ($ticket && !Auth::guard('web')->check()) {
            $ticketRecord = SsoTicket::where('ticket', $ticket)
                ->where('client_key', '3e')
                ->first();

            if ($ticketRecord && $ticketRecord->markAsUsed()) {
                $user = UserAuth::find($ticketRecord->id_emp);
                if ($user && $user->is_active) {
                    Auth::guard('web')->login($user);
                    Log::info("{$logTag}: SSO login success user={$user->id_emp}");
                } else {
                    Log::warning("{$logTag}: ticket valid but user not found/inactive id_emp={$ticketRecord->id_emp}");
                }
            } else {
                Log::warning("{$logTag}: invalid or expired ticket={$ticket}");
            }
        }

        if (!Auth::guard('web')->check()) {
            throw new HttpResponseException($this->guestResponse($request));
        }

        return Auth::guard('web')->user();
    }

    /**
     * คำตอบสำหรับผู้ที่ยังไม่ได้ login
     * - เรียกแบบ AJAX/JSON → 401 พร้อม url ของหน้า login
     * - เปิดหน้าเว็บปกติ    → redirect ไปหน้า login (จำหน้าเดิมไว้ด้วย)
     */
    protected function guestResponse(Request $request)
    {
        if ($request->expectsJson() || $request->ajax() || $request->is('api/*') || $request->is('apis/*')) {
            return response()->json([
                'ok'       => false,
                'message'  => 'เซสชันหมดอายุ กรุณาเข้าสู่ระบบใหม่',
                'redirect' => route('login'),
            ], 401);
        }

        return redirect()->guest(route('login'));
    }
}
