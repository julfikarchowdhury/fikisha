<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MailSettingsController extends Controller
{
    public function index()
    {
        $mail = [
            'mailer' => env('MAIL_MAILER', config('mail.default', 'smtp')),
            'host' => env('MAIL_HOST', (string) data_get(config('mail.mailers.smtp'), 'host', '')),
            'port' => env('MAIL_PORT', (string) data_get(config('mail.mailers.smtp'), 'port', '')),
            'username' => env('MAIL_USERNAME', (string) data_get(config('mail.mailers.smtp'), 'username', '')),
            'password' => env('MAIL_PASSWORD', (string) data_get(config('mail.mailers.smtp'), 'password', '')),
            'encryption' => env('MAIL_ENCRYPTION', (string) data_get(config('mail.mailers.smtp'), 'encryption', 'tls')),
            'from_address' => env('MAIL_FROM_ADDRESS', (string) config('mail.from.address', '')),
            'from_name' => env('MAIL_FROM_NAME', (string) config('mail.from.name', '')),
        ];

        return view('backend.setting.mail.index', compact('mail'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mailer' => ['required', 'string', 'in:smtp'],
            'host' => ['required', 'string', 'max:191'],
            'port' => ['required', 'integer', 'min:1', 'max:65535'],
            'username' => ['nullable', 'string', 'max:191'],
            'password' => ['nullable', 'string', 'max:191'],
            'encryption' => ['nullable', 'string', 'in:tls,ssl'],
            'from_address' => ['required', 'email', 'max:191'],
            'from_name' => ['required', 'string', 'max:191'],
        ]);

        $this->updateEnvValue('MAIL_MAILER', $validated['mailer']);
        $this->updateEnvValue('MAIL_HOST', $validated['host']);
        $this->updateEnvValue('MAIL_PORT', (string) $validated['port']);
        $this->updateEnvValue('MAIL_USERNAME', (string) ($validated['username'] ?? ''));
        $this->updateEnvValue('MAIL_PASSWORD', (string) ($validated['password'] ?? ''));
        $this->updateEnvValue('MAIL_ENCRYPTION', (string) ($validated['encryption'] ?? 'tls'));
        $this->updateEnvValue('MAIL_FROM_ADDRESS', $validated['from_address']);
        $this->updateEnvValue('MAIL_FROM_NAME', $validated['from_name']);

        Artisan::call('optimize:clear');

        Toastr::success('Mail configuration updated successfully.', __('message.success'));
        return redirect()->route('mail-settings.index');
    }

    private function updateEnvValue(string $key, string $value): void
    {
        $path = base_path('.env');
        if (!file_exists($path)) {
            return;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            return;
        }

        $escapedValue = str_contains($value, ' ') ? '"' . addslashes($value) . '"' : $value;
        $pattern = "/^" . preg_quote($key, '/') . "=.*/m";

        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $key . '=' . $escapedValue, $content) ?? $content;
        } else {
            $content .= PHP_EOL . $key . '=' . $escapedValue;
        }

        file_put_contents($path, $content);
    }
}

