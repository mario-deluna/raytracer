<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

final class Matrix
{
    /**
     * @var array
     */
    private $elements;

    public static function fromArray(array $elements): self
    {
        return new self($elements);
    }

    public static function identity(int $size): self
    {
        $elements = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($i === $j) {
                    $elements[$i][$j] = 1.0;
                } else {
                    $elements[$i][$j] = 0.0;
                }
            }
        }

        return new self($elements);
    }

    public function __construct(array $elements)
    {
        $this->ensureSize($elements);
        $this->ensureOnlyFloats($elements);

        $this->elements = $elements;
    }

    public function element(int $i, int $j): float
    {
        return $this->elements[$i][$j];
    }

    public function size(): int
    {
        return \count($this->elements);
    }

    public function equalTo(self $that): bool
    {
        $size = $this->size();

        if ($this->size() !== $that->size()) {
            return false;
        }

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                if ($this->elements[$i][$j] !== $that->element($i, $j)) {
                    return false;
                }
            }
        }

        return true;
    }

    public function multiply(self $that): self
    {
        $size   = $this->size();
        $result = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$i][$j] = 0.0;
            }
        }

        for ($i = 0; $i < $size; $i++) {
            for ($k = 0; $k < $size; $k++) {
                for ($j = 0; $j < $size; $j++) {
                    $result[$i][$k] += $this->elements[$i][$j] * $that->element($j, $k);
                }
            }
        }

        return new self($result);
    }

    public function multiplyBy(Tuple $tuple): Tuple
    {
        if ($this->size() !== 4) {
            throw new RuntimeException(
                'Multiplication of matrix and tuple is only implemented for 4x4 matrices'
            );
        }

        $result = [0.0, 0.0, 0.0, 0.0];

        for ($i = 0; $i < 4; $i++) {
            $result[$i] += $this->elements[$i][0] * $tuple->x();
            $result[$i] += $this->elements[$i][1] * $tuple->y();
            $result[$i] += $this->elements[$i][2] * $tuple->z();
            $result[$i] += $this->elements[$i][3] * $tuple->w();
        }

        return Tuple::create(...$result);
    }

    public function transpose(): self
    {
        $size   = $this->size();
        $result = [];

        for ($i = 0; $i < $size; $i++) {
            for ($j = 0; $j < $size; $j++) {
                $result[$j][$i] = $this->elements[$i][$j];
            }
        }

        return new self($result);
    }

    public function determinant(): float
    {
        $size = $this->size();

        if ($size === 2) {
            return $this->elements[0][0] * $this->elements[1][1] -
                   $this->elements[0][1] * $this->elements[1][0];
        }

        $determinant = 0.0;

        for ($i = 0; $i < $size; $i++) {
            $determinant += $this->cofactor(0, $i) * $this->elements[0][$i];
        }

        return $determinant;
    }

    public function submatrix(int $row, int $column): self
    {
        $elements = [];
        $tmp      = [];
        $size     = $this->size();

        for ($i = 0; $i < $size; $i++) {
            if ($i === $row) {
                continue;
            }

            for ($j = 0; $j < $size; $j++) {
                if ($j === $column) {
                    continue;
                }

                $tmp[$i][$j] = $this->elements[$i][$j];
            }
        }

        foreach (\array_keys($tmp) as $key) {
            $elements[] = \array_values($tmp[$key]);
        }

        return new self($elements);
    }

    public function minor(int $row, int $column): float
    {
        return $this->submatrix($row, $column)->determinant();
    }

    public function cofactor(int $row, int $column): float
    {
        $minor = $this->minor($row, $column);

        if ($row + $column % 2 !== 0) {
            $minor *= -1.0;
        }

        return $minor;
    }

    public function invertible(): bool
    {
        return $this->determinant() !== 0.0;
    }

    private function ensureSize(array $elements): void
    {
        $numberOfRows = \count($elements);

        foreach ($elements as $row) {
            if (\count($row) !== $numberOfRows) {
                throw new InvalidArgumentException(
                    'Elements do not describe a MxM matrix'
                );
            }
        }
    }

    private function ensureOnlyFloats(array $elements): void
    {
        foreach ($elements as $row) {
            foreach ($row as $element) {
                if (!\is_float($element)) {
                    throw new InvalidArgumentException(
                        'Elements are not only float values'
                    );
                }
            }
        }
    }
}
