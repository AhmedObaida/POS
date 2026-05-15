<?php

namespace Tests\Feature;

use Tests\TestCase;

class LocaleSwitchTest extends TestCase
{
    public function test_post_locale_sets_session_and_applies_on_next_request()
    {
        $this->from(route('login'))
            ->post(route('locale.switch'), ['locale' => 'ar'])
            ->assertSessionHas('locale', 'ar')
            ->assertRedirect();

        $this->get(route('login'));
        $this->assertSame('ar', app()->getLocale());
    }

    public function test_post_locale_validates_supported_locales()
    {
        $this->post(route('locale.switch'), ['locale' => 'xx'])
            ->assertSessionHasErrors('locale');
    }
}
