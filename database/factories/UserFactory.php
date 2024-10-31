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
            'username'       => $faker->firstName (),
            'path_profile'   => $faker->imageUrl (),
            'role'           => $faker->randomElement ( [ 'Pegawai', 'Admin', 'Bos' ] ),
            'sex'            => $faker->randomElement ( [ 'Laki-laki', 'Perempuan' ] ),
            'phone'          => $faker->unique ()->phoneNumber (),
            'email'          => $faker->unique ()->email (),
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
}
