<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Material
{
    private float $ambient;

    private float $diffuse;

    private float $specular;

    private float $shininess;

    public static function default(): self
    {
        return self::from(0.1, 0.9, 0.9, 200.0);
    }

    public static function from(float $ambient, float $diffuse, float $specular, float $shininess): self
    {
        return new self($ambient, $diffuse, $specular, $shininess);
    }

    private function __construct(float $ambient, float $diffuse, float $specular, float $shininess)
    {
        $this->ambient   = $ambient;
        $this->diffuse   = $diffuse;
        $this->specular  = $specular;
        $this->shininess = $shininess;
    }

    public function ambient(): float
    {
        return $this->ambient;
    }

    public function diffuse(): float
    {
        return $this->diffuse;
    }

    public function specular(): float
    {
        return $this->specular;
    }

    public function shininess(): float
    {
        return $this->shininess;
    }
}
