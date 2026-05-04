<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\Currency\CurrencyInterface;
use Illuminate\Http\Request;
use App\Repositories\GeneralSettings\GeneralSettingsInterface;
use Brian2694\Toastr\Facades\Toastr;

class GeneralSettingsController extends Controller
{
    protected $repo, $currency;
    public function __construct(
        GeneralSettingsInterface $repo,
        CurrencyInterface $currency
    ) {
        $this->repo     = $repo;
        $this->currency = $currency;
    }

    public function index()
    {
        $settings   = $this->repo->all();
        $currencies = $this->currency->getActive();
        return view('backend.general_settings.index', compact('settings', 'currencies'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'mobile_app_logo' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048', 'dimensions:min_width=512,min_height=512,ratio=1/1'],
        ]);

        if ($this->repo->update($request)) {
            Toastr::success(__('settings.save_change'), __('message.success'));
            return redirect()->back();
        } else {
            $errorMessage = $this->repo->getLastError() ?: __('income.error_msg');
            Toastr::error($errorMessage, __('message.error'));
            return redirect()->back()->withInput();
        }
    }
}
