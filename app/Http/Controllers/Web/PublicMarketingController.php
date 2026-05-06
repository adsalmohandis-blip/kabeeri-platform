<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Ui\V11PublicExperience;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicMarketingController extends Controller
{
    public function landing(): View
    {
        return $this->page('landing');
    }

    public function audiences(): View
    {
        return $this->page('audiences');
    }

    public function business(): View
    {
        return $this->page('business');
    }

    public function enterprise(): View
    {
        return $this->page('enterprise');
    }

    public function developers(): View
    {
        return $this->page('developers');
    }

    public function partners(): View
    {
        return $this->page('partners');
    }

    public function wordpress(): View
    {
        return $this->page('wordpress');
    }

    public function serviceBusiness(): View
    {
        return $this->page('service_business');
    }

    public function templates(): View
    {
        return $this->page('templates');
    }

    public function onboarding(): View
    {
        return $this->page('onboarding');
    }

    public function workspaceSetup(): View
    {
        return $this->page('workspace_setup');
    }

    public function pricing(): View
    {
        return $this->page('pricing');
    }

    public function trust(): View
    {
        return $this->page('trust');
    }

    public function contact(): View
    {
        return $this->page('contact');
    }

    public function storeInquiry(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'audience' => ['required', 'string', 'max:80'],
            'plan_interest' => ['nullable', 'string', 'max:80'],
            'company_name' => ['nullable', 'string', 'max:160'],
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190'],
            'phone' => ['nullable', 'string', 'max:80'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        V11PublicExperience::captureInquiry($payload);

        return redirect()
            ->route('public.contact')
            ->with('status', 'تم استلام طلبك. سنراجعه داخل CRM ونحدد الخطوة التالية.');
    }

    private function page(string $page): View
    {
        return view('public.v11-page', [
            'data' => V11PublicExperience::pageData($page),
        ]);
    }
}
