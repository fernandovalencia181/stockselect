<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SiteSetting;

class MaintenanceModeMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Nunca bloquear el panel de administración
        if ($request->is('admin*') || $request->is('livewire*') || $request->is('filament*')) {
            return $next($request);
        }

        try {
            $maintenance = SiteSetting::where('key', 'maintenance_mode')->value('value');
            
            if ($maintenance === '1' || $maintenance === true || $maintenance === 'true') {
                return response()->view('errors.503', [], 503);
            }
        } catch (\Exception $e) {
            // Si hay un error con la DB, simplemente continuamos
        }

        return $next($request);
    }
}
