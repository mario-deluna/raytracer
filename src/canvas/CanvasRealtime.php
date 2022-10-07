<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

use function range;
use IteratorAggregate;
use GL\Buffer\UByteBuffer;

final class CanvasRealtime implements IteratorAggregate, CanvasInterface
{
    private int $width;
    private int $height;

    public UByteBuffer $pixels;

    public static function from(int $width, int $height, Color $background): self
    {
        return new self($width, $height, $background);
    }

    public function __construct(int $width, int $height, Color $background)
    {
        $this->width  = $width;
        $this->height = $height;

        $this->initializePixels($background);
    }

    public function width(): int
    {
        return $this->width;
    }

    public function height(): int
    {
        return $this->height;
    }

    public function pixelAt(int $x, int $y): Color
    {
        $offset = ($y * $this->width + $x) * 3;
        $r = $this->pixels[$offset] / 255;
        $g = $this->pixels[$offset + 1] / 255;
        $b = $this->pixels[$offset + 2] / 255;
        return Color::from($r, $g, $b);
    }

    public function writePixel(int $x, int $y, Color $c): void
    {
        $offset = ($y * $this->width + $x) * 3;
        $this->pixels[$offset] = $c->redAsInt();
        $this->pixels[$offset + 1] = $c->greenAsInt();
        $this->pixels[$offset + 2] = $c->blueAsInt();
    }

    public function getIterator(): CanvasIterator
    {
        return new CanvasIterator($this);
    }

    private function initializePixels(Color $background): void
    {
        $this->pixels = new UByteBuffer();
        // @todo use the actual background color instead of just black
        $this->pixels->fill(($this->width() * $this->height() * 4) + 1, 0);
    }
}
