<?php

namespace Database\Factories;

use App\Models\Form;
use App\Models\FormField;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FormField>
 */
class FormFieldFactory extends Factory
{
    protected $model = FormField::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $label = fake()->unique()->words(2, true);

        return [
            'form_id' => Form::factory(),
            'label' => Str::title($label),
            'name' => Str::slug($label, '_').'_'.fake()->unique()->numberBetween(1000, 9999),
            'field_type' => 'text',
            'placeholder' => fake()->optional()->words(3, true),
            'help_text' => fake()->optional()->sentence(),
            'is_required' => false,
            'validation_rules' => null,
            'options' => null,
            'sort_order' => fake()->numberBetween(0, 20),
            'settings' => ['width' => 'full'],
        ];
    }
}
