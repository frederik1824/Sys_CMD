<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethod('POST') || $request->isMethod('PUT') || $request->isMethod('PATCH')) {
            $input = $request->all();

            array_walk_recursive($input, function (&$value, $key) {
                if (is_string($value)) {
                    // Do not touch passwords or token
                    if (!in_array($key, ['password', 'password_confirmation', '_token'])) {
                        
                        // Si no es cédula, aplicamos Anti-XSS completo
                        if (!in_array($key, ['cedula', 'cedula_identidad'])) {
                            $value = strip_tags($value);
                        }
                        
                        // Trim leading and trailing spaces everywhere
                        $value = trim($value);

                        // Estandarizar Nombres a formato Título (Capitalize)
                        if (in_array($key, ['nombre_completo', 'nombre', 'contacto_nombre'])) {
                            if (function_exists('mb_convert_case')) {
                                $value = mb_convert_case(mb_strtolower($value, 'UTF-8'), MB_CASE_TITLE, 'UTF-8');
                            } else {
                                $value = ucwords(strtolower($value));
                            }
                        }
                    }
                }
            });

            $request->merge($input);
        }

        return $next($request);
    }
}
