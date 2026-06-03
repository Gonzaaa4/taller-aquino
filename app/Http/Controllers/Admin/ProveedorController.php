<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index(Request $request)
    {
        $proveedores = Proveedor::withCount('repuestos')
            ->when($request->buscar, fn($q) => $q->where('nombre', 'like', "%{$request->buscar}%"))
            ->when($request->categoria, fn($q) => $q->where('categoria', $request->categoria))
            ->orderBy('nombre')
            ->paginate(15);

        return view('admin.proveedores.index', compact('proveedores'));
    }

    public function create()
    {
        return view('admin.proveedores.form', ['proveedor' => new Proveedor()]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        Proveedor::create($datos);
        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor registrado correctamente.');
    }

    public function edit(Proveedor $proveedor)
    {
        return view('admin.proveedores.form', compact('proveedor'));
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $datos = $this->validar($request, $proveedor->id);
        $proveedor->update($datos);
        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        // No eliminar si tiene repuestos asociados
        if ($proveedor->repuestos()->count() > 0) {
            $proveedor->update(['activo' => false]);
            return back()->with('success', 'Proveedor desactivado (tiene repuestos asociados).');
        }
        $proveedor->delete();
        return redirect()->route('admin.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }

    private function validar(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'nombre'    => 'required|string|max:200',
            'telefono'  => 'nullable|string|max:30',
            'email'     => 'nullable|email|max:200',
            'direccion' => 'nullable|string|max:300',
            'categoria' => 'nullable|string|max:100',
            'notas'     => 'nullable|string',
            'activo'    => 'boolean',
        ]);
    }
}
