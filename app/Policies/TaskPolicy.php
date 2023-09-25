<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Task;

class TaskPolicy
{
    //para el metodo index del controlador TaskController
    public function index(User $user, Task $task): bool
    {
        // Si el rol es controller podra listar todos los contactos de cualquier cliente
        if ($user->role_name === 'administrador') {
            return true;
        }
    }

    // para el metodo show de TaskController
    public function show(User $user, Task $task): bool
    {
        // Si el rol es administradi podra ver cualquier tarea de cualquier usuario
        if ($user->role_name === 'administrador') {
            return true;
        } else {
            // solo podra ver su tarea si le pertenece
            return $user->id === $task->id;
        }
    }

    // para el metodo update de TaskController
    public function update(User $user, Task $task): bool
    {
        // Si el rol es administrador puede actualizar cualquier tarea de cualquier usuario
        if ($user->role_name === 'administrador') {
            return true;
        } else {
            // solo podra actualizar sus tareas si le pertenece
            return $user->id === $task->id;
        }
    }

    // para el metodo destroy de TaskController
    public function destroy(User $user, Task $task): bool
    {
        // Si el rol es administradi podra eliminar cualquier tarea de cualquier usuario
        if ($user->role_name === 'administrador') {
            return true;
        } else {
            // solo podra eliminar  su tarea si le pertenece
            return $user->id === $task->id;
        }
    }
}
