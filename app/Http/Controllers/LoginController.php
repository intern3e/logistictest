<?php

namespace App\Http\Controllers;

use App\Models\UserAuth;
use App\Models\SsoClient;
use App\Models\SsoTicket;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
public function showLoginForm(Request $request)
{
    // ★ ไม่ต้องรับ/ส่ง sso_token ผ่าน query string อีกต่อไป
    return view('login.login');
}

public function login(Request $request)
{
    $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
    ]);

    $ssoIntent = session('sso_intent');

    // ★★★ ใส่ debug ตรงนี้ ★★★
    Log::info('DEBUG login: session_id=' . session()->getId() . ' intent=' . json_encode($ssoIntent));

    $user = UserAuth::where('username', $request->username)->first();

    if (!$user || !$user->is_active) {
        return back()->withErrors(['username' => 'ไม่พบผู้ใช้งานนี้']);
    }

    if ($user->password !== $request->password) {
        return back()->withErrors(['password' => 'รหัสผ่านไม่ถูกต้อง']);
    }

    Auth::guard('web')->loginUsingId($user->id_emp);

    if ($ssoIntent && !empty($ssoIntent['client']) && !empty($ssoIntent['return_url'])) {
        session()->forget('sso_intent'); // ★ ใช้แล้วลบทิ้ง กัน replay
        Log::info("Login: Issue ticket for client={$ssoIntent['client']}");
        return $this->issueTicketAndRedirect($user, $ssoIntent['client'], $ssoIntent['return_url']);
    }

    Log::info("Login: No SSO intent → redirect to home");
    return redirect()->intended(route('home'));     
}

public function ssoAuthorize(Request $request)
{
    $clientKey = $request->query('client');
    $returnUrl = $request->query('return_url');

    $returnUrl = preg_replace('/[?&]ticket=[^&]*/', '', $returnUrl);
    $returnUrl = rtrim($returnUrl, '?&');

    Log::info("SSO Authorize: client={$clientKey}, logged_in=" . (Auth::check() ? 'yes' : 'no'));

    $client = SsoClient::find($clientKey);
    if (!$client || !$client->isCallbackAllowed($returnUrl)) {
        abort(400, 'Invalid client or callback URL');
    }

    if (!Auth::guard('web')->check()) {
        session(['sso_intent' => [
            'client'     => $clientKey,
            'return_url' => $returnUrl,
        ]]);

        Log::info("SSO Authorize: Saved intent to session");

        // ★★★ ใส่ debug ตรงนี้ ★★★
        Log::info('DEBUG authorize: session_id=' . session()->getId() . ' intent=' . json_encode(session('sso_intent')));

        return redirect()->route('login');
    }

    return $this->issueTicketAndRedirect(Auth::user(), $clientKey, $returnUrl);
}
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function ssoVerify(Request $request): JsonResponse
    {
        $request->validate([
            'ticket'        => 'required|string|max:64',
            'client_key'    => 'required|string|max:20',
            'client_secret' => 'required|string|max:64',
        ]);

        $client = SsoClient::find($request->client_key);
        if (!$client || !hash_equals($client->client_secret, $request->client_secret)) {
            return response()->json(['success' => false, 'error' => 'Invalid credentials'], 401);
        }

        $ticketRecord = SsoTicket::where('ticket', $request->ticket)
            ->where('client_key', $request->client_key)
            ->first();

        if (!$ticketRecord || !$ticketRecord->markAsUsed()) {
            return response()->json(['success' => false, 'error' => 'Invalid or expired ticket'], 400);
        }

        $user = UserAuth::find($ticketRecord->id_emp);
        if (!$user || !$user->is_active) {
            return response()->json(['success' => false, 'error' => 'User inactive'], 403);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id_emp'       => $user->id_emp,
                'username'     => $user->username,
                'name'         => $user->name,
                'auth'         => $user->auth,
                'role'         => $user->role,
                'auth_version' => $user->auth_version,
            ],
        ]);
    }

// ssoLogout() — เอา bounce กลับ server_update ออก กัน loop
public function ssoLogout(Request $request)
{
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $finalRedirect = $request->query('redirect_url', '/');
    return redirect($finalRedirect); // ไม่ redirect ไป server_update/logout ซ้ำ
}
private function issueTicketAndRedirect(UserAuth $user, string $clientKey, string $returnUrl)
{
    $client = SsoClient::find($clientKey);
    if (!$client || !$client->isCallbackAllowed($returnUrl)) {
        abort(400, 'Invalid client or callback');
    }

    $ticket = SsoTicket::create([
        'ticket'     => Str::random(64),
        'id_emp'     => $user->id_emp,
        'client_key' => $clientKey,
        'expires_at' => now()->addSeconds(60),
        'used'       => false,
    ]);

    Log::info("Ticket issued for user={$user->id_emp} client={$clientKey}");

    // ★ action ต้องเป็น callback_url ของ client ไม่ใช่ returnUrl
    $action = e($client->callback_url);
    $ticketValue = e($ticket->ticket);
    // ★ ส่ง return_url แนบไปด้วย เผื่อ client ต้องรู้ปลายทางจริงหลัง verify เสร็จ
    $returnUrlValue = e($returnUrl);

    $html = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>กำลังเข้าสู่ระบบ...</title></head>
<body>
    <p>กำลังเข้าสู่ระบบ กรุณารอสักครู่...</p>
    <form id="ssoForm" method="POST" action="{$action}">
        <input type="hidden" name="ticket" value="{$ticketValue}">
        <input type="hidden" name="return_url" value="{$returnUrlValue}">
    </form>
    <script>document.getElementById('ssoForm').submit();</script>
</body>
</html>
HTML;

    return response($html);
}
    public function issueTicket(Request $request): JsonResponse
    {
        $client = SsoClient::find($request->client_key);
        if (!$client || !hash_equals($client->client_secret, $request->client_secret)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        $user = UserAuth::find($request->id_emp);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $ticket = SsoTicket::create([
            'ticket'     => Str::random(64),
            'id_emp'     => $user->id_emp,
            'client_key' => $request->client_key,
            'expires_at' => now()->addSeconds(60),
            'used'       => false,
        ]);

        return response()->json(['ticket' => $ticket->ticket]);
    }
}