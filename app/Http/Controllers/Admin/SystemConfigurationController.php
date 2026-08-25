<?php

namespace App\Http\Controllers\Admin;

use App\Model\SystemConfiguration;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SystemConfigurationController extends Controller
{
    public function showSystemConfiguration()
    {
        $configuration = SystemConfiguration::first();
        return view('admin.system_configuration', compact('configuration'));
    }

    public function updateSystemConfiguration(Request $request)
    {
        $request->validate([
            'campaign_permission' => 'required|integer|min:0|max:1'
        ]);
        try {
            $configuration = SystemConfiguration::first();
            if (empty($configuration)) {
                $configuration = new SystemConfiguration();
            }
            $configuration->campaign_permission = $request->campaign_permission;
            $configuration->save();

        } catch (\Exception $e) {
            session()->flash('type', 'danger');
            session()->flash('message', 'Something went wrong!');

            return redirect()->back();
        }

        session()->flash('type', 'success');
        session()->flash('message', 'Configuration Updated Successfully Done.');
        return redirect()->back();

    }
}
