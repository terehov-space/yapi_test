<?php

namespace App\Http\Controllers;

use App\Services\LightService;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class TestController extends Controller
{
    public function index(Request $request, LightService $lightService)
    {
        $needAuth = false;

        try {
            $devicesList = $lightService->getDevicesList(Session::get('access_token'));
        } catch (\Throwable $e) {
            $needAuth = true;
        }

        if ($needAuth) {
            return redirect('https://oauth.yandex.ru/authorize?response_type=token&client_id=df34fe37c4ab40c580a1a3e08e9bb3d0');
        }

        return Inertia::render('Light', ['devices' => $devicesList]);
    }

    public function light(Request $request, LightService $lightService)
    {
        $devices = $request->post('devices');
        $color = $request->post('color');

        $lightService->setDevicesColor(Session::get('access_token'), $devices, ...$color);
    }

    public function auth(Request $request, LightService $lightService)
    {
        if ($request->has('access_token') && $request->has('token_type')) {
            $token = $request->get('access_token');

            Session::put('access_token', $token);

            $devices = $lightService->getDevicesList($token);

            return redirect('/');
        }

        return view('auth');
    }
}
