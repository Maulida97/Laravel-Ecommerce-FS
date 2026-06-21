<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that guests (unauthenticated) cannot access admin routes.
     */
    public function test_unauthenticated_user_is_redirected_from_admin_dashboard_and_settings(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');

        $responseSettings = $this->get('/admin/settings');
        $responseSettings->assertRedirect('/login');
    }

    /**
     * Test that standard non-admin users (e.g. role = member) cannot access admin routes.
     */
    public function test_non_admin_user_gets_forbidden_status_from_admin_dashboard_and_settings(): void
    {
        $user = User::factory()->create([
            'role' => 'customer', // standard customer role
        ]);

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);

        $responseSettings = $this->actingAs($user)->get('/admin/settings');
        $responseSettings->assertStatus(403);
    }

    /**
     * Test that admin users can access the admin dashboard.
     */
    public function test_admin_user_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Overview');
    }

    /**
     * Test that admin users can access the settings view.
     */
    public function test_admin_user_can_access_settings_page(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/settings');
        $response->assertStatus(200);
        $response->assertSee('Settings');
        $response->assertSee('General Information');
        $response->assertSee('Midtrans Payment Gateway');
    }

    /**
     * Test settings update saves correct values to the database.
     */
    public function test_admin_user_can_update_settings_successfully(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'store_name' => 'Modified Toko Name',
            'store_tagline' => 'New Tagline for testing',
            'contact_email' => 'admin-test@tokoku.id',
            'contact_phone' => '+6289999999',
            'contact_address' => 'Test address st. 55',
            'default_shipping_cost' => 12500.50,
            'free_shipping_threshold' => 200000,
            'social_twitter' => 'https://twitter.com/mytestbrand',
            'social_instagram' => 'https://instagram.com/mytestbrand',
            'social_facebook' => 'https://facebook.com/mytestbrand',
        ]);

        $response->assertRedirect('/admin/settings');
        $response->assertSessionHas('success');

        // Check values in DB
        $this->assertEquals('Modified Toko Name', Setting::getValue('store_name'));
        $this->assertEquals('New Tagline for testing', Setting::getValue('store_tagline'));
        $this->assertEquals('admin-test@tokoku.id', Setting::getValue('contact_email'));
        $this->assertEquals('+6289999999', Setting::getValue('contact_phone'));
        $this->assertEquals('Test address st. 55', Setting::getValue('contact_address'));
        $this->assertEquals('12500.5', Setting::getValue('default_shipping_cost'));
        $this->assertEquals('200000', Setting::getValue('free_shipping_threshold'));
        $this->assertEquals('https://twitter.com/mytestbrand', Setting::getValue('social_twitter'));
        $this->assertEquals('https://instagram.com/mytestbrand', Setting::getValue('social_instagram'));
        $this->assertEquals('https://facebook.com/mytestbrand', Setting::getValue('social_facebook'));
    }

    /**
     * Test that Midtrans API keys are encrypted when saved in database and decrypted when loading page.
     */
    public function test_admin_user_midtrans_keys_are_encrypted_in_database(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Submit the keys via form
        $response = $this->actingAs($admin)->post('/admin/settings', [
            'store_name' => 'Toko Enkripsi',
            'contact_email' => 'support@toko.id',
            'contact_phone' => '1234',
            'contact_address' => 'Street',
            'default_shipping_cost' => 0,
            'free_shipping_threshold' => 0,
            'midtrans_server_key' => 'super-secret-server-key',
            'midtrans_client_key' => 'super-secret-client-key',
            'midtrans_sandbox_mode' => 'true',
        ]);

        $response->assertRedirect('/admin/settings');

        // Verify database holds ENCRYPTED strings, meaning they shouldn't equal the plaintext
        $dbServerKey = Setting::where('key', 'midtrans_server_key')->first()->value;
        $dbClientKey = Setting::where('key', 'midtrans_client_key')->first()->value;

        $this->assertNotEquals('super-secret-server-key', $dbServerKey);
        $this->assertNotEquals('super-secret-client-key', $dbClientKey);

        // Decrypt using Crypt to verify they are properly encrypted
        $this->assertEquals('super-secret-server-key', Crypt::decryptString($dbServerKey));
        $this->assertEquals('super-secret-client-key', Crypt::decryptString($dbClientKey));
        $this->assertEquals('true', Setting::getValue('midtrans_sandbox_mode'));

        // Visit the setting form and assert we see the decrypted value
        $getViewResponse = $this->actingAs($admin)->get('/admin/settings');
        $getViewResponse->assertStatus(200);
        $getViewResponse->assertSee('super-secret-server-key');
        $getViewResponse->assertSee('super-secret-client-key');
    }

    /**
     * Test that admin can upload store logo locally to public disk storage.
     */
    public function test_admin_user_can_upload_logo_locally(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $logoFile = UploadedFile::fake()->image('brand-logo.png');

        $response = $this->actingAs($admin)->post('/admin/settings', [
            'store_name' => 'Toko Logo',
            'contact_email' => 'support@toko.id',
            'contact_phone' => '1234',
            'contact_address' => 'Street',
            'default_shipping_cost' => 0,
            'free_shipping_threshold' => 0,
            'store_logo' => $logoFile,
        ]);

        $response->assertRedirect('/admin/settings');

        // Verify the file was stored under logo/ directory on public disk
        Storage::disk('public')->assertExists('logo/' . $logoFile->hashName());

        // Get the logo URL saved in settings
        $storedLogoUrl = Setting::getValue('store_logo');
        $this->assertStringContainsString('logo/' . $logoFile->hashName(), $storedLogoUrl);
    }
}
