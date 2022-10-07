<?php declare(strict_types=1);
namespace SebastianBergmann\Raytracer;

require_once __DIR__ . '/../src/autoload.php';
/**
 * Config
 * 
 * ----------------------------------------------------------------------------
 */
$renderWidth = 100;
$renderHeight = 50;

$windowScale = 8;
$windowWidth = $renderWidth * $windowScale;
$windowHeight = $renderHeight * $windowScale;

/**
 * Initalize GLFW, create window and prepare for rendering
 * 
 * ----------------------------------------------------------------------------
 */
glfwInit();
glfwWindowHint(GLFW_CONTEXT_VERSION_MAJOR, 4);
glfwWindowHint(GLFW_CONTEXT_VERSION_MINOR, 1);
glfwWindowHint(GLFW_OPENGL_PROFILE, GLFW_OPENGL_CORE_PROFILE);
glfwWindowHint(GLFW_OPENGL_FORWARD_COMPAT, GL_TRUE);
glfwWindowHint(GLFW_RESIZABLE, GL_FALSE);

if (!$window = glfwCreateWindow($windowWidth, $windowHeight, "Raytracer", null, null)) {
    throw new \Exception("Failed to create window");
}

glfwMakeContextCurrent($window);
glfwSwapInterval(1);

// allocate a texture for the rendered image
glGenTextures(1, $texture);
glActiveTexture(GL_TEXTURE0);
glBindTexture(GL_TEXTURE_2D, $texture);
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_S, GL_REPEAT);
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_WRAP_T, GL_REPEAT);
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MIN_FILTER, GL_NEAREST);
glTexParameteri(GL_TEXTURE_2D, GL_TEXTURE_MAG_FILTER, GL_NEAREST);

//  create a vertex array with 6 vertices for the quad (not indexed)
$verticies = new \GL\Buffer\FloatBuffer([
    -1.0, -1.0, 0.0, 1.0,
    1.0, -1.0, 1.0, 1.0,
    1.0, 1.0, 1.0, 0.0,
    1.0, 1.0, 1.0, 0.0,
    -1.0, 1.0, 0.0, 0.0,
    -1.0, -1.0, 0.0, 1.0
]);

// create a vertex array object and upload the vertices
glGenVertexArrays(1, $VAO);
glGenBuffers(1, $VBO);

glBindVertexArray($VAO);
glBindBuffer(GL_ARRAY_BUFFER, $VBO);
glBufferData(GL_ARRAY_BUFFER, $verticies, GL_STATIC_DRAW);

// declare the vertex attributes
// positions
glVertexAttribPointer(0, 2, GL_FLOAT, false, GL_SIZEOF_FLOAT * 4, 0);
glEnableVertexAttribArray(0);

// uv
glVertexAttribPointer(1, 2, GL_FLOAT, false, GL_SIZEOF_FLOAT * 4, GL_SIZEOF_FLOAT * 2);
glEnableVertexAttribArray(1);

// unbind
glBindBuffer(GL_ARRAY_BUFFER, 0); 
glBindVertexArray(0); 

// create a simple shader to just render the texture

// create, upload and compile the vertex shader
$vertexShader = glCreateShader(GL_VERTEX_SHADER);
glShaderSource($vertexShader, <<< 'GLSL'
#version 330 core
layout (location = 0) in vec2 position;
layout (location = 1) in vec2 uv;

out vec2 v_uv;

void main()
{
    gl_Position = vec4(position, 0.0, 1.0);
    v_uv = uv;
}
GLSL);
glCompileShader($vertexShader);
glGetShaderiv($vertexShader, GL_COMPILE_STATUS, $success);
if (!$success) {
    throw new Exception("Vertex shader could not be compiled.");
}

// create, upload and compile the fragment shader
$fragShader = glCreateShader(GL_FRAGMENT_SHADER);
glShaderSource($fragShader, <<<'GLSL'
#version 330 core
out vec4 fragment_color;
in vec2 v_uv;

uniform sampler2D u_texture;

void main()
{
    fragment_color = texture(u_texture, v_uv);
} 
GLSL);
glCompileShader($fragShader);
glGetShaderiv($fragShader, GL_COMPILE_STATUS, $success);
if (!$success) {
    throw new Exception("Fragment shader could not be compiled.");
}

// create a shader programm and link our vertex and framgent shader together
$shaderProgram = glCreateProgram();
glAttachShader($shaderProgram, $vertexShader);
glAttachShader($shaderProgram, $fragShader);
glLinkProgram($shaderProgram);

glGetProgramiv($shaderProgram, GL_LINK_STATUS, $linkSuccess);
if (!$linkSuccess) {
    throw new Exception("Shader program could not be linked.");
}

// free the shders
glDeleteShader($vertexShader);
glDeleteShader($fragShader);

/**
 * Builds the scene
 * 
 * ----------------------------------------------------------------------------
 */
$floor = Sphere::default();
$floor->setTransform(Transformations::scaling(10, 0.01, 10));
$floorMaterial = Material::default();
$floorMaterial->setColor(Color::from(1, 0.9, 0.9));
$floorMaterial->setSpecular(0);
$floor->setMaterial($floorMaterial);

$leftWall = Sphere::default();
$leftWall->setTransform(
    Transformations::translation(0, 0, 5)->multiply(
        Transformations::rotationAroundY(-M_PI_4)
    )->multiply(
        Transformations::rotationAroundX(M_PI_2)
    )->multiply(
        Transformations::scaling(10, 0.01, 10)
    )
);
$leftWall->setMaterial($floorMaterial);

$rightWall = Sphere::default();
$rightWall->setTransform(
    Transformations::translation(0, 0, 5)->multiply(
        Transformations::rotationAroundY(M_PI_4)
    )->multiply(
        Transformations::rotationAroundX(M_PI_2)
    )->multiply(
        Transformations::scaling(10, 0.01, 10)
    )
);
$rightWall->setMaterial($floorMaterial);

$middle = Sphere::default();
$middle->setTransform(Transformations::translation(-0.5, 1, 0.5));
$middleMaterial = Material::default();
$middleMaterial->setColor(Color::from(0.1, 1, 0.5));
$middleMaterial->setDiffuse(0.7);
$middleMaterial->setSpecular(0.3);
$middle->setMaterial($middleMaterial);

$right = Sphere::default();
$right->setTransform(Transformations::translation(1.5, 0.5, -0.5)->multiply(Transformations::scaling(0.5, 0.5, 0.5)));
$rightMaterial = Material::default();
$rightMaterial->setColor(Color::from(0.5, 1, 0.1));
$rightMaterial->setDiffuse(0.7);
$rightMaterial->setSpecular(0.3);
$right->setMaterial($rightMaterial);

$left = Sphere::default();
$left->setTransform(Transformations::translation(-1.5, 0.33, -0.75)->multiply(Transformations::scaling(0.33, 0.33, 0.33)));
$leftMaterial = Material::default();
$leftMaterial->setColor(Color::from(1, 0.8, 0.1));
$leftMaterial->setDiffuse(0.7);
$leftMaterial->setSpecular(0.3);
$left->setMaterial($leftMaterial);

$world = new World;
$world->add($floor);
$world->add($leftWall);
$world->add($rightWall);
$world->add($middle);
$world->add($right);
$world->add($left);
$world->setLight(PointLight::from(Tuple::point(-10, 10, -10), Color::from(1, 1, 1)));

$camera = Camera::from(100, 50, M_PI / 3);
$camera->setTransform(
    Transformations::view(
        Tuple::point(0, 1.5, -5),
        Tuple::point(0, 1, 0),
        Tuple::vector(0, 1, 0)
    )
);


/**
 * Render loop, pixel by pixel
 * 
 * ----------------------------------------------------------------------------
 */

// Create realtime canvas and render scene
$canvas = CanvasRealtime::from($renderWidth, $renderHeight, Color::from(0, 0, 0));

// render loop
$currentX = 0;
$currentY = 0;

// lets try to keep 30fps
$frameTime = 1 / 30;

// flag if we should pause rendering
$pause = false;

// counter to step a pixel calucaltion if set to > 0
$stepper = 0;

// register a key listener
glfwSetKeyCallback($window, function ($key, $scancode, $action, $mods) use (&$pause, $window, &$stepper) {
    // pause
    if ($key == GLFW_KEY_SPACE && $action == GLFW_PRESS) {
        $pause = !$pause;
    }
    // exit on essc
    if ($key == GLFW_KEY_ESCAPE && $action == GLFW_PRESS) {
        glfwSetWindowShouldClose($window, GLFW_TRUE);
    }
    // step on "s"
    if ($key == GLFW_KEY_S && ($action == GLFW_PRESS || $action == GLFW_REPEAT)) {
        $stepper = 1;
    }
});

// print out a small help text
echo str_repeat('-', 80) . PHP_EOL;
echo ' -> Press "space" to pause rendering' . PHP_EOL;
echo ' -> Press "ESC" to exit' . PHP_EOL;
echo ' -> Press "S" to step pixel by pixel while paused.' . PHP_EOL;
echo str_repeat('-', 80) . PHP_EOL;

while (!glfwWindowShouldClose($window)) 
{
    glClearColor(0.0, 0.0, 0.0, 1.0);
    glClear(GL_COLOR_BUFFER_BIT);

    // if we reached the end of the canvas, we always set pause to true
    if ($currentY >= $renderHeight) {
        $pause = true;
    }

    $timeBudget = $frameTime;
    $startTime = microtime(true);
    while (($timeBudget > 0 && $pause === false) || $stepper > 0) {
        $ray   = $camera->rayForPixel($currentX, $currentY);
        $color = $world->colorAt($ray);
        $canvas->writePixel($currentX, $currentY, $color);

        // update x and y
        $currentX++;
        if ($currentX >= $renderWidth) {
            $currentX = 0;
            $currentY++;
        }

        // check if we are done
        if ($currentY >= $renderHeight) {
            break;
        }

        if ($stepper > 0) {
            $stepper--;
        }

        $timeBudget -= microtime(true) - $startTime;
    }

    // upload the canvas buffer to the texture
    glTexImage2D(GL_TEXTURE_2D, 0, GL_RGB, $renderWidth, $renderHeight, 0, GL_RGB, GL_UNSIGNED_BYTE, $canvas->pixels);

    // render the texture
    glUseProgram($shaderProgram);
    glUniform1i(glGetUniformLocation($shaderProgram, "u_texture"), 0);
    glBindVertexArray($VAO);
    glDrawArrays(GL_TRIANGLES, 0, 6);


    glfwSwapBuffers($window);
    glfwPollEvents();
}

/**
 * Cleanup
 */
glfwDestroyWindow($window);
glfwTerminate();
