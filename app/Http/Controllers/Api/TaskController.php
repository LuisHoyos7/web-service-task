<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;


class TaskController extends Controller
{
    protected $task;

    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    public function index()
    {
        try {
            // realizamos una instancia de la politica para saber su respuesta
            $authorization = Gate::inspect('index', [$this->task]);

            //si no esta autorizado le devolvemos un 403
            if (!$authorization->allowed()) {
                //devolvemos un mensaje con acceso no autorizado
                return response()->json(['success' => false, 'message'   => 'Acceso no autorizado'], 403);
            }
            //Devolvemos el resource de las tareas paginado
            return new TaskCollection($this->task->orderBy('id', 'DESC')->paginate(10));
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error al listar las tareas' . $e->getMessage()], 500);
        }
    }

    public function store(TaskRequest $request)
    {
        try {
            //Creamos la tarea
            $data = $request->all();
            $data["user_id"] = Auth::user()->id;
            $createTask = $this->task->create($data);

            return response()->json([
                'success' => true,
                'message' => 'Tarea creada con éxito',
                'task' => $createTask
            ], 201);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error al guardar tarea' . $e->getMessage()], 500);
        }
    }

    public function show(Task $task)
    {
        // Preguntamos si el usuario logueado esta autorizado
        $authorization = Gate::inspect('show', $task);

        //si no esta autorizado devolvemos un 403
        if (!$authorization->allowed()) {
            //devolvemos un mensaje con acceso no autorizado
            return response()->json(['success' => false, 'message'   => 'Acceso no autorizado'], 403);
        }

        //si esta autorizado devolvemos la tarea
        return new TaskResource($task);
    }

    public function update(TaskRequest $request, Task $task)
    {
        try {
            // Preguntamos si el usuario logueado esta autorizado
            $authorization = Gate::inspect('update', $task);

            //si no esta autorizado devolvemos un 403
            if (!$authorization->allowed()) {
                //devolvemos un mensaje con acceso no autorizado
                return response()->json(['success' => false, 'message'   => 'Acceso no autorizado'], 403);
            }
            //Inyectamos el modelo en el controller por medio de la ruta y actualizamos
            $task->update($request->all());

            //Si esta autorizado devolvemos la respuesta
            return new TaskResource($task);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error al actualizar la tarea' . $e->getMessage()], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            // Preguntamos si el usuario logueado esta autorizado
            $authorization = Gate::inspect('destroy', $task);

            //si no esta autorizado devolvemos un 403
            if (!$authorization->allowed()) {
                //devolvemos un mensaje con acceso no autorizado
                return response()->json(['success' => false, 'message'   => 'Acceso no autorizado'], 403);
            }
            //Eliminamos la tarea
            $task->delete();

            // si esta autorizado devolvemos la respuesta
            return new TaskResource($task);
        } catch (\Exception $e) {
            // Manejo de errores genéricos
            return response()->json(['success' => false, 'message' => 'Error al eliminar la tarea' . $e->getMessage()], 500);
        }
    }
}
