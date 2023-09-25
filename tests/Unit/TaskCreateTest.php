<?php

namespace Tests\Unit;

use Illuminate\Database\Eloquent\Factories\Factory;
use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Http\Controllers\TaskController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;

class TaskCreateTest extends TestCase
{
    use  WithFaker;

    /** @test */
    public function it_can_create_a_task()
    {
        Artisan::call("migrate");
        // Paso 1: Crear un usuario ficticio (o utilizar un usuario existente si es necesario).
        $user = User::factory()->create();

        // Paso 2: Crear datos ficticios para la tarea.
        $taskData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'state_id' => 1,
            'priority_id' => 1
        ];

        // Paso 3: Enviar una solicitud POST al controlador para crear la tarea.
        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        // Paso 4: Verificar que la respuesta sea exitosa (código de estado 201) y tiene la estructura esperada.
        $response->assertStatus(201) // Comprobar que la solicitud haya tenido éxito (código 201 - Created).
            ->assertJsonStructure([ // Comprobar la estructura de la respuesta JSON esperada.
                'success',
                'message',
                'task' => [
                    'id',
                    'title',
                    'description',
                    'created_at', // Agregamos 'created_at' y 'updated_at'.
                    'updated_at',
                    'user_id', // Agregamos 'user_id'.
                ],
            ])
            ->assertJson([ // Comprobar los datos específicos que esperas en la respuesta JSON.
                'success' => true,
                'message' => 'Tarea creada con éxito',
                'task' => [
                    'title' => $taskData['title'],
                    'description' => $taskData['description'],
                    'user_id' => $user->id, // Agregamos el ID del usuario que creó la tarea.
                    // Agregar más campos según la estructura de tu modelo Task.
                ],
            ]);
    }

    /** @test */
    public function it_requires_title_when_creating_a_task()
    {
        Artisan::call("migrate");
        // Paso 1: Crear un usuario ficticio (o utilizar un usuario existente si es necesario).
        $user = User::factory()->create();

        // Paso 2: Crear datos ficticios para la tarea sin un título.
        $taskData = [
            'description' => $this->faker->paragraph,
        ];

        // Paso 3: Enviar una solicitud POST al controlador sin un título.
        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        // Paso 4: Verificar la respuesta.
        $response->assertStatus(422) // Comprobar que la solicitud haya fallado debido a la validación (código 422 - Unprocessable Entity).
            ->assertJsonValidationErrors(['title']); // Comprobar que la validación haya generado un error para el campo 'title'.
    }

    /** @test */
    public function it_handles_error_when_creating_task()
    {
        Artisan::call("migrate");

        $taskData = [
            'title' => $this->faker->sentence,
            'description' => 40,
            'user_id'  => 1,
            'priority_id' => 1,
            'state_id' => "Error"
        ];
        // Paso 1: Crear un usuario ficticio (o utilizar un usuario existente si es necesario).
        $user = User::factory()->create();

        // Paso 2: Enviar una solicitud POST al controlador para crear la tarea.
        $response = $this->actingAs($user)->post(route('tasks.store'), $taskData);

        $response->assertStatus(500) // Comprobar que la solicitud haya fallado debido al error interno del servidor (código 500).
            ->assertJsonStructure([ // Comprobar la estructura de la respuesta JSON esperada.
                'success',
                'message',
            ]);
    }

    public function testIndexUnauthorized()
    {
        Artisan::call("migrate");
        // Crear una tarea de ejemplo (puedes usar una fábrica o crearla manualmente)


        // Llamar al método 'index' del controlador como un usuario no autorizado
        $response = $this->get('/api/tasks');

        // Verificar que se recibe una respuesta con un código 403 (acceso no autorizado)
        $response->assertStatus(401);

        // Verificar que la respuesta contiene el mensaje esperado
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }


    public function testDestroyUnauthorized()
    {
        Artisan::call("migrate");
        // Crear una tarea de ejemplo
        $task = [
            'id' => 8,
            'user_id' => 1,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'state_id' => 1,
            'priority_id' => 1
        ];


        // Llamar al método 'destroy' del controlador
        $response = $this->delete("/api/tasks/{$task["id"]}");

        // Verificar que se recibe una respuesta con un código 403 (acceso no autorizado)
        $response->assertStatus(401);

        // Verificar que la respuesta contiene el mensaje esperado
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }

    public function testUpdateUnauthorized()
    {
        Artisan::call("migrate");
        // Crear una tarea de ejemplo
        $task = [
            'id' => 8,
            'user_id' => 1,
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'state_id' => 1,
            'priority_id' => 1
        ];

        // Datos de actualización simulados
        $updateData = [
            'id' => 8,
            'title' => 'Nuevo título',
            'description' => 'Nueva descripción',
            'state_id' => 2,
            'priority_id' => 2,
        ];



        // Llamar al método 'update' del controlador
        $response = $this->put("/api/tasks/{$task["id"]}", $updateData);

        // Verificar que se recibe una respuesta con un código 403 (acceso no autorizado)
        $response->assertStatus(401);

        // Verificar que la respuesta contiene el mensaje esperado
        $response->assertJsonStructure([
            'success',
            'message',
        ]);
    }
}
