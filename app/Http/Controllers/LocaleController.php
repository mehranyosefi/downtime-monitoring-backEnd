<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Config;

class LocaleController extends Controller
{
    public function index(string $id)
    {
        $locales = Config::get('constants.LOCALES');
        if (!in_array($id, $locales)) abort(400, "locale not valid");
        $data = Config::get("constants.LANGS.{$id}");
        return response()->json(["data" => $data], 200, ['Content-Type' => 'application/json;', 'Charset' => 'utf-8']);
    }
}
