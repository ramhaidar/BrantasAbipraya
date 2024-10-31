<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class FirstGroupFactory extends Factory
{
    public function definition () : array
    {
        $names = array_merge ( array_map ( function ($number)
        {
            return "A{$number}";
        }, range ( 1, 24 ) ), [ 'B3', 'C1' ] );
        return [ 
            'name' => $this->faker->randomElement ( $names ),
        ];
    }
}