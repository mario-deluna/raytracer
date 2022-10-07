[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D%208.1-8892BF.svg?style=flat-square)](https://php.net/)
[![CI Status](https://github.com/sebastianbergmann/raytracer/workflows/CI/badge.svg?branch=master&event=push)](https://github.com/sebastianbergmann/raytracer/actions)
[![Type Coverage](https://shepherd.dev/github/sebastianbergmann/raytracer/coverage.svg)](https://shepherd.dev/github/sebastianbergmann/raytracer)
[![Code Coverage](https://codecov.io/gh/sebastianbergmann/raytracer/branch/main/graph/badge.svg?token=IfbgdrCpOM)](https://codecov.io/gh/sebastianbergmann/raytracer)

# Raytracer

Code written while reading the book "The Ray Tracer Challenge: A Test-Driven Guide to Your First 3D Renderer" by Jamis Buck.

This is example code that is not production-ready. It is intended for studying and learning purposes.

(c) Sebastian Bergmann. All rights reserved.

## This Fork 

![raytracer 2022-10-07 09_22_44](https://user-images.githubusercontent.com/956212/194496387-c6121e1c-cadf-43c4-a88c-e33e4781ca8d.gif)

In this fork I simply added the ability to preview the rendering progress in realtime. This requires the [php-glfw](https://github.com/mario-deluna/php-glfw) extension to be installed tho.

All credit for this project goes to Sebastian Bergmann tho, this really just takes the pixel data and outputs it into a window. 

To run the realtime preview just exectue:

```bash
php tools/preview.php
```

The preview renders one fixed scene right now, and does not support selecting different ones without changing the code in `preview.php`.


## Progress

- [X] Chapter 1: Tuples, Points, and Vectors
- [X] Chapter 2: Drawing on a Canvas
- [X] Chapter 3: Matrices
- [X] Chapter 4: Matrix Transformations
- [X] Chapter 5: Ray-Sphere Intersections
- [X] Chapter 6: Light and Shading
- [X] Chapter 7: Making a Scene
- [X] Chapter 8: Shadows
- [X] Chapter 9: Planes
- [X] Chapter 10: Patterns
- [ ] Chapter 11: Reflection and Refraction
- [ ] Chapter 12: Cubes
- [ ] Chapter 13: Cylinders
- [ ] Chapter 14: Groups
- [ ] Chapter 15: Triangles
- [ ] Chapter 15: Constructive Solid Geometry (CSG)
