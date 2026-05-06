<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Support\Ui\V12MarketplaceExperience;
use Illuminate\View\View;

class MarketplaceDeveloperController extends Controller
{
    public function marketplaceHome(): View
    {
        return $this->page('marketplace_home');
    }

    public function themeCatalog(): View
    {
        return $this->page('theme_catalog');
    }

    public function themeDetail(string $theme): View
    {
        return $this->page('theme_detail', $theme);
    }

    public function themeRecipes(): View
    {
        return $this->page('theme_recipes');
    }

    public function pluginCatalog(): View
    {
        return $this->page('plugin_catalog');
    }

    public function pluginDetail(string $package): View
    {
        return $this->page('plugin_detail', $package);
    }

    public function licensing(): View
    {
        return $this->page('licensing');
    }

    public function governance(): View
    {
        return $this->page('governance');
    }

    public function reviewStatus(): View
    {
        return $this->page('review_status');
    }

    public function developerLanding(): View
    {
        return $this->page('developer_landing');
    }

    public function developerOnboarding(): View
    {
        return $this->page('developer_onboarding');
    }

    public function themeBuilderDocs(): View
    {
        return $this->page('theme_builder_docs');
    }

    public function pluginManifestDocs(): View
    {
        return $this->page('plugin_manifest_docs');
    }

    public function connectorSdkDocs(): View
    {
        return $this->page('connector_sdk_docs');
    }

    public function submissionChecklist(): View
    {
        return $this->page('submission_checklist');
    }

    public function developerListings(): View
    {
        return $this->page('developer_listings');
    }

    public function developerSales(): View
    {
        return $this->page('developer_sales');
    }

    public function developerProfile(): View
    {
        return $this->page('developer_profile');
    }

    public function qaCenter(): View
    {
        return $this->page('qa_center');
    }

    private function page(string $page, ?string $slug = null): View
    {
        return view('marketplace.v12-page', [
            'data' => V12MarketplaceExperience::pageData($page, $slug),
        ]);
    }
}
