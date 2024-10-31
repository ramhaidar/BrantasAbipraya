<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class SecondGroupFactory extends Factory
{
    public function definition () : array
    {
        return [ 
            'name' => $this->faker->randomElement ( array_map ( function ($number)
            {
                return "B{$number}";
            }, range ( 11, 29 ) ) ),
        ];
    }
}