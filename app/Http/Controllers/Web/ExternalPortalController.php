<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Ui\V13ExternalExperience;
use Illuminate\View\View;

class ExternalPortalController extends Controller
{
    public function mallSearch(): View
    {
        return $this->page('mall_search');
    }

    public function mallTrust(): View
    {
        return $this->page('mall_trust');
    }

    public function mallClaimReport(): View
    {
        return $this->page('mall_claim_report');
    }

    public function customerDashboard(): View
    {
        return $this->page('customer_dashboard');
    }

    public function customerThemePlugins(): View
    {
        return $this->page('customer_theme_plugins');
    }

    public function customerQuickSetup(): View
    {
        return $this->page('customer_quick_setup');
    }

    public function partnerLanding(): View
    {
        return $this->page('partner_landing');
    }

    public function agencyProfile(): View
    {
        return $this->page('agency_profile');
    }

    public function partnerStorefront(): View
    {
        return $this->page('partner_storefront');
    }

    public function referralDashboard(): View
    {
        return $this->page('referral_dashboard');
    }

    public function campaignResources(): View
    {
        return $this->page('campaign_resources');
    }

    public function networkAcademy(): View
    {
        return $this->page('network_academy');
    }

    public function talentPath(): View
    {
        return $this->page('talent_path');
    }

    public function legalVerification(): View
    {
        return $this->page('legal_verification');
    }

    private function page(string $page): View
    {
        return view('external.v13-page', [
            'data' => V13ExternalExperience::pageData($page),
        ]);
    }
}
