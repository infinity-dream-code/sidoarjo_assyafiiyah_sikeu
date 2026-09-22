<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\CyberKey;
use App\Support\PersistentLogin;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function showLoginForm(Request $request): View
    {
        if ($request->has("cf_fallback")) {
            if ($request->boolean("cf_fallback")) {
                session(["auth_cf_fallback" => true]);
            } else {
                session()->forget(["auth_cf_fallback", "auth_math_answer"]);
            }
        }

        if ($this->shouldUseMathFallback($request)) {
            $challenge = $this->makeMathChallengePayload();

            return view("auth.login", [
                "useMathFallback" => true,
                "mathLeft" => $challenge["left"],
                "mathOperator" => $challenge["operator"],
                "mathRight" => $challenge["right"],
                "mathImage" => $challenge["image"],
            ]);
        }

        return view("auth.login", [
            "useMathFallback" => false,
            "mathImage" => null,
        ]);
    }

    protected function validateLogin(Request $request): void
    {
        $rules = [
            "username" => "required|string",
            "password" => "required|string",
        ];

        if ($this->shouldVerifyTurnstile()) {
            $rules["cf-turnstile-response"] = "required|string";
        } elseif ($this->shouldUseMathFallback($request)) {
            $rules["math_answer"] = "required|integer";
        }

        $request->validate($rules, [
            "username.required" => "Silakan isi username.",
            "password.required" => "Silakan isi password.",
            "cf-turnstile-response.required" =>
                "Silakan selesaikan verifikasi captcha.",
            "math_answer.required" => "Silakan isi hasil perhitungan.",
            "math_answer.integer" => "Jawaban hitung harus berupa angka.",
        ]);
    }

    protected function attemptLogin(Request $request)
    {
        if ($this->shouldVerifyTurnstile()) {
            $this->verifyTurnstile($request);
        } elseif ($this->shouldUseMathFallback($request)) {
            $this->verifyMathFallback($request);
        }

        $username = trim((string) $request->input("username"));
        $password = (string) $request->input("password");

        $user = CyberKey::query()->where("users", $username)->first();

        if (!$user || empty($user->password)) {
            $this->throwInvalidCredentials();
        }

        $storedPassword = strtolower(trim((string) $user->password));
        $inputHash = strtolower(md5($password));

        if ($inputHash !== $storedPassword) {
            $this->throwInvalidCredentials();
        }

        $this->guard()->login($user);
        PersistentLogin::queue($user);
        session()->forget(["auth_cf_fallback", "auth_math_answer"]);

        return true;
    }

    protected function authenticated(Request $request, $user)
    {
        PersistentLogin::queue($user);
    }

    protected function verifyTurnstile(Request $request): void
    {
        $secret = config("services.turnstile.secret_key");
        $token = (string) $request->input("cf-turnstile-response", "");

        if ($token === "") {
            $this->failTurnstileAndUseFallback(
                "Token captcha kosong. Halaman dialihkan ke login non-Cloudflare.",
            );
        }

        if (!filled($secret)) {
            $this->failTurnstileAndUseFallback(
                "Secret key Turnstile belum diset di server. Gunakan login non-Cloudflare.",
            );
        }

        try {
            $response = Http::timeout(8)->asForm()->post(
                "https://challenges.cloudflare.com/turnstile/v0/siteverify",
                [
                    "secret" => $secret,
                    "response" => $token,
                    "remoteip" => $request->ip(),
                ],
            );
        } catch (Throwable $exception) {
            Log::warning("Turnstile siteverify unreachable", [
                "message" => $exception->getMessage(),
            ]);

            $this->failTurnstileAndUseFallback(
                "Layanan Cloudflare tidak dapat diakses. Halaman dialihkan ke login non-Cloudflare.",
            );
        }

        $payload = $response->json();
        if (!($payload["success"] ?? false)) {
            Log::warning("Turnstile siteverify rejected token", [
                "error_codes" => $payload["error-codes"] ?? [],
                "host" => $request->getHost(),
            ]);

            $this->failTurnstileAndUseFallback(
                "Verifikasi Cloudflare gagal di server (biasanya secret key tidak cocok atau domain belum diizinkan). Halaman dialihkan ke login non-Cloudflare.",
            );
        }
    }

    private function failTurnstileAndUseFallback(string $message): void
    {
        session(["auth_cf_fallback" => true]);

        throw ValidationException::withMessages([
            "turnstile" => [$message],
        ])->redirectTo(route("login", ["cf_fallback" => 1]));
    }

    protected function verifyMathFallback(Request $request): void
    {
        $expected = (int) session("auth_math_answer", PHP_INT_MIN);
        $answer = (int) $request->input("math_answer", PHP_INT_MAX);

        if ($expected === PHP_INT_MIN || $answer !== $expected) {
            throw ValidationException::withMessages([
                "math_answer" => [
                    "Jawaban hitung tidak sesuai. Silakan coba lagi.",
                ],
            ]);
        }

        session()->forget("auth_math_answer");
    }

    protected function shouldVerifyTurnstile(): bool
    {
        if ($this->shouldUseMathFallback(request())) {
            return false;
        }

        if (!config("services.turnstile.enabled", true)) {
            return false;
        }

        if (!filled(config("services.turnstile.secret_key"))) {
            return false;
        }

        $host = request()->getHost();

        if (
            in_array($host, ["localhost", "127.0.0.1"], true) ||
            str_ends_with($host, ".test") ||
            str_ends_with($host, ".local")
        ) {
            return false;
        }

        return true;
    }

    protected function shouldUseMathFallback(?Request $request = null): bool
    {
        $request ??= request();

        if ($request->boolean("cf_fallback")) {
            return true;
        }

        return (bool) session("auth_cf_fallback", false);
    }

    private function buildMathChallenge(): array
    {
        $left = random_int(1, 40);
        $right = random_int(1, 40);
        $operator = random_int(0, 1) === 1 ? "+" : "-";

        if ($operator === "-" && $left < $right) {
            [$left, $right] = [$right, $left];
        }

        $result = $operator === "+" ? ($left + $right) : ($left - $right);

        return [$left, $operator, $right, $result];
    }

    private function makeMathChallengePayload(): array
    {
        [$left, $operator, $right, $expected] = $this->buildMathChallenge();
        session(["auth_math_answer" => $expected]);

        return [
            "left" => $left,
            "operator" => $operator,
            "right" => $right,
            "expected" => $expected,
            "image" => $this->buildMathChallengeImage($left, $operator, $right),
        ];
    }

    private function buildMathChallengeImage(int $left, string $operator, int $right): string
    {
        $text = "{$left} {$operator} {$right} = ?";
        $escaped = htmlspecialchars($text, ENT_QUOTES | ENT_XML1, "UTF-8");

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="280" height="84" viewBox="0 0 280 84" role="img" aria-label="{$escaped}">
  <defs>
    <linearGradient id="bg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#eef2ff"/>
      <stop offset="100%" stop-color="#dbe7ff"/>
    </linearGradient>
  </defs>
  <rect width="280" height="84" rx="12" fill="url(#bg)" stroke="#c5d0f0"/>
  <g stroke="#b7c4ea" stroke-width="1" opacity="0.55">
    <line x1="18" y1="22" x2="62" y2="66"/>
    <line x1="70" y1="18" x2="118" y2="70"/>
    <line x1="140" y1="16" x2="210" y2="72"/>
    <line x1="220" y1="20" x2="262" y2="64"/>
    <circle cx="48" cy="28" r="3" fill="#9db0e8" stroke="none"/>
    <circle cx="168" cy="58" r="2.5" fill="#9db0e8" stroke="none"/>
    <circle cx="236" cy="34" r="3" fill="#9db0e8" stroke="none"/>
  </g>
  <text x="140" y="52" text-anchor="middle" font-family="Segoe UI, Arial, sans-serif" font-size="30" font-weight="700" fill="#3b4a8a" letter-spacing="1">{$escaped}</text>
</svg>
SVG;

        return "data:image/svg+xml;base64," . base64_encode($svg);
    }

    public function reloadMathCaptcha(Request $request)
    {
        if (!$this->shouldUseMathFallback($request)) {
            session(["auth_cf_fallback" => true]);
        }

        $challenge = $this->makeMathChallengePayload();

        return response()->json([
            "image" => $challenge["image"],
            "label" => "{$challenge["left"]} {$challenge["operator"]} {$challenge["right"]}",
        ]);
    }

    protected function redirectTo(): string
    {
        return "/admin";
    }

    public function __construct()
    {
        $this->middleware("guest")->except(["logout", "reloadMathCaptcha"]);
    }

    protected function sendFailedLoginResponse(Request $request): void
    {
        $this->throwInvalidCredentials();
    }

    protected function throwInvalidCredentials(): void
    {
        throw ValidationException::withMessages([
            "username" => [
                "Username atau password salah. Periksa kembali data login Anda.",
            ],
        ]);
    }

    public function username(): string
    {
        return "username";
    }

    public function reloadCaptcha()
    {
        return response()->json(["captcha" => captcha_src()]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        PersistentLogin::forget();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->session()->forget(["auth_cf_fallback", "auth_math_answer"]);

        return redirect("/");
    }
}
