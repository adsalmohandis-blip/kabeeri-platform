<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\FormField;
use App\Models\Organization;
use App\Models\Site;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FormsCoreFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_form_is_tenant_scoped_and_can_have_fields(): void
    {
        $organization = Organization::factory()->create();
        $site = Site::factory()->create(['organization_id' => $organization->id]);

        $form = Form::factory()->create([
            'organization_id' => $organization->id,
            'site_id' => $site->id,
            'name' => 'Contact Us',
            'slug' => 'contact-us',
            'status' => 'draft',
        ]);

        $nameField = FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Full Name',
            'name' => 'full_name',
            'field_type' => 'text',
            'is_required' => true,
            'validation_rules' => ['string', 'max:255'],
            'sort_order' => 10,
        ]);
        $emailField = FormField::factory()->create([
            'form_id' => $form->id,
            'label' => 'Email',
            'name' => 'email',
            'field_type' => 'email',
            'is_required' => true,
            'validation_rules' => ['email'],
            'sort_order' => 20,
        ]);

        $this->assertSame($organization->id, $form->organization->id);
        $this->assertSame($site->id, $form->site->id);
        $this->assertTrue($organization->forms()->whereKey($form->id)->exists());
        $this->assertTrue($site->forms()->whereKey($form->id)->exists());
        $this->assertSame([$nameField->id, $emailField->id], $form->fields()->pluck('id')->all());
        $this->assertTrue($nameField->is_required);
        $this->assertSame(['string', 'max:255'], $nameField->validation_rules);
    }

    public function test_field_names_are_unique_per_form(): void
    {
        $form = Form::factory()->create();
        $otherForm = Form::factory()->create([
            'organization_id' => $form->organization_id,
            'site_id' => $form->site_id,
        ]);

        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
        ]);
        FormField::factory()->create([
            'form_id' => $otherForm->id,
            'name' => 'email',
        ]);

        $this->expectException(QueryException::class);

        FormField::factory()->create([
            'form_id' => $form->id,
            'name' => 'email',
        ]);
    }
}
