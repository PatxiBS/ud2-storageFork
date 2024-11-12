<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;


class HelloWorldController extends Controller
{
    /**
     * Lista todos los ficheros de la carpeta storage/app.
     *
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: Un array con los nombres de los ficheros.
     */
    public function index()
    {
        $files = Storage::files();

        return response()->json([
            'mensaje' => 'Listado de ficheros',
            'contenido' => $files
        ]);
    }

     /**
     * Recibe por parámetro el nombre de fichero y el contenido. Devuelve un JSON con el resultado de la operación.
     * Si el fichero ya existe, devuelve un 409.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function store(Request $request)
    {
    // Validar los parámetros 'filename' y 'content'
    $validator = Validator::make($request->all(), [
        'filename' => 'required|string',
        'content' => 'required|string',
    ]);

    // Devolver un error 422 si los parámetros no son válidos
    if ($validator->fails()) {
        return response()->json([
            'mensaje' => 'Faltan parámetros requeridos o son inválidos.',
        ], 422);
    }

    $filename = $request->input('filename');
    $content = $request->input('content');

    // Verificar si el archivo ya existe
    if (Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo ya existe',
        ], 409);
    }

    // Crear y guardar el archivo con el contenido proporcionado
    Storage::put($filename, $content);

    // Devolver una respuesta de éxito
    return response()->json([
        'mensaje' => 'Guardado con éxito',
    ], 200);
    }

     /**
     * Recibe por parámetro el nombre de fichero y devuelve un JSON con su contenido
     *
     * @param name Parámetro con el nombre del fichero.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     * - contenido: El contenido del fichero si se ha leído con éxito.
     */
    public function show(string $filename)
    {
        // Verificar si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'Archivo no encontrado',
        ], 404); // Código 404 para recurso no encontrado
    }

    // Intentar leer el contenido del archivo
    $contenido = Storage::get($filename);

    return response()->json([
        'mensaje' => 'Archivo leído con éxito',
        'contenido' => $contenido,
    ], 200); // Código 200 para éxito
    }

    /**
     * Recibe por parámetro el nombre de fichero, el contenido y actualiza el fichero.
     * Devuelve un JSON con el resultado de la operación.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @param content Contenido del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function update(Request $request, string $filename)
    {
            // Validar que el contenido esté presente en la solicitud
    $validator = Validator::make($request->all(), [
        'content' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'mensaje' => 'El contenido es obligatorio',
        ], 422); // Código 422 para error de validación
    }

    // Verificar si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404); // Código 404 si el archivo no existe
    }

    // Actualizar el contenido del archivo
    $content = $request->input('content');
    Storage::put($filename, $content);

    return response()->json([
        'mensaje' => 'Actualizado con éxito',
    ], 200);
    }

    /**
     * Recibe por parámetro el nombre de ficher y lo elimina.
     * Si el fichero no existe devuelve un 404.
     *
     * @param filename Parámetro con el nombre del fichero. Devuelve 422 si no hay parámetro.
     * @return JsonResponse La respuesta en formato JSON.
     *
     * El JSON devuelto debe tener las siguientes claves:
     * - mensaje: Un mensaje indicando el resultado de la operación.
     */
    public function destroy(string $filename)
    {
         // Verificar si el archivo existe
    if (!Storage::exists($filename)) {
        return response()->json([
            'mensaje' => 'El archivo no existe',
        ], 404); // Código 404 si el archivo no existe
    }

    // Eliminar el archivo
    Storage::delete($filename);

    return response()->json([
        'mensaje' => 'Eliminado con éxito',
    ], 200); // Código 200 para éxito
    }
}
