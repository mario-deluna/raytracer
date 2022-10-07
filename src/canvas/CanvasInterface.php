<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function range;

interface CanvasInterface {

    public function __construct(int $width, int $height, Color $background);

    public function width(): int;

    public function height(): int;

    public function pixelAt(int $x, int $y): Color;

    public function writePixel(int $x, int $y, Color $c): void;

    public function getIterator(): CanvasIterator;
}
