<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Blade;

class BladeComponentsTest extends TestCase
{
    public function test_input_component_renders_correctly()
    {
        $rendered = Blade::render('<x-input name="user_email" type="email" label="Email Address" value="test@example.com" icon="bi-envelope" col="6" required />');
        
        $this->assertStringContainsString('id="field_user_email"', $rendered);
        $this->assertStringContainsString('name="user_email"', $rendered);
        $this->assertStringContainsString('type="email"', $rendered);
        $this->assertStringContainsString('value="test@example.com"', $rendered);
        $this->assertStringContainsString('Email Address', $rendered);
        $this->assertStringContainsString('col-md-6', $rendered);
        $this->assertStringContainsString('bi-envelope', $rendered);
        $this->assertStringContainsString('required', $rendered);
    }

    public function test_select_component_renders_correctly_with_selected_option()
    {
        $options = ['active' => 'Active Status', 'inactive' => 'Inactive Status'];
        $rendered = Blade::render('<x-select name="status" label="Status" :options="$options" selected="inactive" col="4" required />', ['options' => $options]);

        $this->assertStringContainsString('id="field_status"', $rendered);
        $this->assertStringContainsString('name="status"', $rendered);
        $this->assertStringContainsString('value="inactive" selected', $rendered);
        $this->assertStringContainsString('Active Status', $rendered);
        $this->assertStringContainsString('Inactive Status', $rendered);
    }

    public function test_date_and_datetime_components_render_correctly()
    {
        $renderedDate = Blade::render('<x-date name="dob" label="Date of Birth" value="1995-05-20" col="6" />');
        $this->assertStringContainsString('type="date"', $renderedDate);
        $this->assertStringContainsString('value="1995-05-20"', $renderedDate);

        $renderedDateTime = Blade::render('<x-datetime name="appointment" label="Appointment" value="2026-08-12 14:30" col="6" />');
        $this->assertStringContainsString('type="datetime-local"', $renderedDateTime);
        $this->assertStringContainsString('value="2026-08-12T14:30"', $renderedDateTime);
    }

    public function test_checkbox_and_radio_components_render_correctly()
    {
        $renderedCb = Blade::render('<x-checkbox name="is_active" label="Enable Feature" checked col="6" />');
        $this->assertStringContainsString('type="checkbox"', $renderedCb);
        $this->assertStringContainsString('checked', $renderedCb);

        $options = ['m' => 'Male', 'f' => 'Female'];
        $renderedRad = Blade::render('<x-radio name="gender" label="Gender" :options="$options" selected="f" col="6" />', ['options' => $options]);
        $this->assertStringContainsString('type="radio"', $renderedRad);
        $this->assertStringContainsString('value="f"', $renderedRad);
        $this->assertStringContainsString('checked', $renderedRad);
    }

    public function test_button_and_badge_components_render_correctly()
    {
        $renderedBtn = Blade::render('<x-button type="submit" variant="success" icon="bi-check-lg" loadingText="Saving...">Save Record</x-button>');
        $this->assertStringContainsString('btn-success', $renderedBtn);
        $this->assertStringContainsString('bi-check-lg', $renderedBtn);
        $this->assertStringContainsString('Save Record', $renderedBtn);

        $renderedBadge = Blade::render('<x-badge variant="info" icon="bi-info-circle">Active</x-badge>');
        $this->assertStringContainsString('bg-info', $renderedBadge);
        $this->assertStringContainsString('Active', $renderedBadge);
    }
}
