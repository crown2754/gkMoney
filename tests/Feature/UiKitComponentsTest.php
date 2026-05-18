<?php

namespace Tests\Feature;

use Tests\TestCase;

class UiKitComponentsTest extends TestCase
{
    public function test_tailwind_theme_exposes_finance_ui_tokens(): void
    {
        $config = file_get_contents(base_path('tailwind.config.js'));

        $this->assertStringContainsString('primary:', $config);
        $this->assertStringContainsString('secondary:', $config);
        $this->assertStringContainsString('success:', $config);
        $this->assertStringContainsString('danger:', $config);
        $this->assertStringContainsString('warning:', $config);
        $this->assertStringContainsString('neutral:', $config);
        $this->assertStringContainsString('boxShadow:', $config);
        $this->assertStringContainsString("'soft'", $config);
        $this->assertStringContainsString('borderRadius:', $config);
    }

    public function test_button_component_supports_variants_sizes_and_attribute_merging(): void
    {
        $view = $this->blade(
            '<x-ui.button variant="danger" size="lg" type="button" class="w-full" data-testid="delete-button">刪除</x-ui.button>'
        );

        $view->assertSee('刪除');
        $view->assertSee('type="button"', false);
        $view->assertSee('data-testid="delete-button"', false);
        $view->assertSee('w-full', false);
        $view->assertSee('bg-danger-600', false);
        $view->assertSee('px-5 py-3 text-base', false);
        $view->assertSee('focus-visible:ring-danger-500', false);
    }

    public function test_input_label_and_card_components_render_accessible_form_surface(): void
    {
        $view = $this->blade(<<<'BLADE'
            <x-ui.card class="space-y-4">
                <x-ui.label for="amount" value="金額" />
                <x-ui.input id="amount" name="amount" error="請輸入金額" class="text-right" />
            </x-ui.card>
        BLADE);

        $view->assertSee('金額');
        $view->assertSee('for="amount"', false);
        $view->assertSee('bg-white', false);
        $view->assertSee('shadow-soft', false);
        $view->assertSee('text-right', false);
        $view->assertSee('border-danger-500', false);
        $view->assertSee('focus:ring-danger-500', false);
    }
}
