<?php

namespace Database\Seeders;

use App\Models\SsoClient;
use Illuminate\Database\Seeder;

/**
 * ตั้งค่า SSO client 'update' (= server_update) ให้ชี้ host ให้ถูกตาม environment
 *
 * ค่ามาจาก config/services.php → .env (SSO_CLIENT_UPDATE_URL)
 *   local: http://192.168.1.169:8000
 *   prod : http://server_update:8000
 *
 * รัน:  php artisan db:seed --class=SsoClientUpdateSeeder
 *
 * - callback_url      = <base>/sso/callback   (จุดที่ logistic POST ticket กลับไป)
 * - allowed_callbacks = ["<base>/"]           (อนุญาต return_url ทุกหน้าใต้ origin นี้)
 * - client_secret     ตั้งเฉพาะตอนสร้างใหม่ (ไม่ทับของเดิม กัน secret prod หาย)
 */
class SsoClientUpdateSeeder extends Seeder
{
    public function run(): void
    {
        $base = rtrim((string) config('services.sso.client_update_url'), '/');

        $client = SsoClient::firstOrNew(['client_key' => 'update']);
        $client->callback_url      = $base . '/sso/callback';
        $client->allowed_callbacks = json_encode([$base . '/'], JSON_UNESCAPED_SLASHES);

        if (!$client->exists) {
            $client->client_secret = (string) config('services.sso.client_update_secret');
        }

        $client->save();

        $this->command?->info("SSO client 'update' -> {$client->callback_url}");
    }
}
