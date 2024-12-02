<?php

namespace Tests\Unit;

use App\Http\Requests\StoreUserRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;
use App\Models\User;

class StoreUserRequestTest extends TestCase
{
    /**
     * Adatok validálása a StoreUserRequest szabályokkal.
     */
    private function validate(array $data): array
    {
        $request = new StoreUserRequest();
        $rules = $request->rules();
        $messages = $request->messages();

        $validator = Validator::make($data, $rules, $messages);

        return $validator->fails() ? $validator->errors()->toArray() : [];
    }

    /**
     * Validáció sikeres adatokkal.
     */
    public function testValidData()
    {
        $data = [
            'name' => 'Teszt Ember',
            'email' => 'teszt.ember@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '06301234567',
            'address' => 'teszt utca 6',
        ];

        $errors = $this->validate($data);

        $this->assertEmpty($errors, 'A validáció nem sikerült érvényes adatokkal.');
    }

    /**
     * Hiányzó kötelező mezők validációja.
     */
    public function testMissingFields()
    {
        $data = []; // Minden mező hiányzik

        $errors = $this->validate($data);

        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('password', $errors);
        $this->assertArrayHasKey('phone_number', $errors);
        $this->assertArrayHasKey('address', $errors);
    }

    /**
     * Érvénytelen e-mail cím validációja.
     */
    public function testInvalidEmail()
    {
        $data = [
            'name' => 'Teszt Ember',
            'email' => 'rossz email',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => '06301234567',
            'address' => 'teszt utca 6',
        ];

        $errors = $this->validate($data);

        $this->assertArrayHasKey('email', $errors);
        $this->assertStringContainsString('Az e-mail cím nem megfelelő formátumú.', $errors['email'][0]);
    }

    /**
 * Duplikált e-mail cím validációja.
 */
public function testDuplicateEmail()
{
    User::create([
        'name' => 'Teszt Ember',
        'email' => 'teszt.ember@example.com',
        'password' => bcrypt('password123'),
        'phone_number' => '06301234567',
        'address' => 'teszt utca 6',
    ]);

    // Duplikált e-mail cím küldése
    $data = [
        'name' => 'Teszt Ember',
        'email' => 'teszt.ember@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'phone_number' => '06301234567',
        'address' => 'teszt utca 6',
    ];

    $errors = $this->validate($data);

    $this->assertArrayHasKey('email', $errors);
    $this->assertStringContainsString('Ez az e-mail cím már használatban van', $errors['email'][0]);

    User::where('email', 'teszt.ember@example.com')->delete();
}


    /**
     * Túl rövid jelszó validációja.
     */
    public function testShortPassword()
    {
        $data = [
            'name' => 'Teszt Ember',
            'email' => 'teszt.ember@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'phone_number' => '06301234567',
            'address' => 'teszt utca 6',
        ];

        $errors = $this->validate($data);

        $this->assertArrayHasKey('password', $errors);
        $this->assertStringContainsString('A jelszónak legalább 8 karakter hosszúnak kell lennie.', $errors['password'][0]);
    }

    /**
     * Érvénytelen telefonszám validációja.
     */
    public function testInvalidPhoneNumber()
    {
        $data = [
            'name' => 'Teszt Ember',
            'email' => 'teszt.ember@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone_number' => 'invalid-phone',
            'address' => 'teszt utca 6',
        ];

        $errors = $this->validate($data);

        $this->assertArrayHasKey('phone_number', $errors);
        $this->assertStringContainsString('A telefonszám nem megfelelő formátumú.', $errors['phone_number'][0]);
    }

    /**
     * Hiányzó jelszó megerősítés validációja.
     */
    public function testMissingPasswordConfirmation()
    {
        $data = [
            'name' => 'Teszt Ember',
            'email' => 'teszt.ember@example.com',
            'password' => 'password123',
            // 'password_confirmation'
            'phone_number' => '06301234567',
            'address' => 'teszt utca 6',
        ];

        $errors = $this->validate($data);

        $this->assertArrayHasKey('password', $errors);
        $this->assertStringContainsString('A jelszavak nem egyeznek meg.', $errors['password'][0]);

    }
}
