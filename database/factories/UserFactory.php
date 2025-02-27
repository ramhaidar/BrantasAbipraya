<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition () : array
    {
        $faker = FakerFactory::create ( 'id_ID' );

        return [ 
            'name'           => $faker->name (),
            'username'       => $faker->unique ()->userName (),
            'path_profile'   => '/UserDefault.png',
            'role'           => $faker->randomElement ( [ 'svp', 'vp', 'admin_divisi', 'koordinator_proyek' ] ),
            'sex'            => $faker->randomElement ( [ 'Laki-laki', 'Perempuan' ] ),
            'phone'          => '08' . $faker->numberBetween ( 1000000000, 9999999999 ),
            'email'          => $faker->unique ()->safeEmail (),
            'password'       => static::$password ??= Hash::make ( 'password' ),
            'remember_token' => Str::random ( 10 ),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified () : static
    {
        return $this->state ( fn ( array $attributes ) => [ 
            'email_verified_at' => null,
        ] );
    }

    public function withCredentials ( string $username, string $name, string $email, string $role, string $password ) : self
    {
        return $this->state ( fn ( array $attributes ) => [ 
            'username' => $username,
            'name'     => $name,
            'email'    => $email,
            'role'     => $role,
            'password' => Hash::make ( $password ),
        ] );
    }
}
