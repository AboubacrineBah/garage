<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class Parametre extends Model
{
    protected $fillable = [
        'cle',
        'valeur',
        'type',
        'groupe',
        'libelle',
        'description',
    ];

    /**
     * Récupère une valeur de paramètre (avec cache).
     */
    public static function get(string $cle, mixed $default = null): mixed
    {
        $param = Cache::remember("parametre.{$cle}", 3600, function () use ($cle) {
            return static::where('cle', $cle)->first();
        });

        if (! $param) {
            return $default;
        }

        return match ($param->type) {
            'int', 'integer' => (int) $param->valeur,
            'bool', 'boolean' => filter_var($param->valeur, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($param->valeur, true),
            default => $param->valeur,
        };
    }

    /**
     * Définit une valeur de paramètre.
     */
    public static function set(string $cle, mixed $valeur, array $attrs = []): self
    {
        $type = $attrs['type'] ?? 'string';

        if (is_array($valeur) || is_object($valeur)) {
            $valeur = json_encode($valeur);
            $type = 'json';
        } elseif (is_bool($valeur)) {
            $valeur = $valeur ? '1' : '0';
            $type = 'bool';
        }

        $param = static::updateOrCreate(
            ['cle' => $cle],
            array_merge($attrs, [
                'valeur' => (string) $valeur,
                'type' => $type,
            ])
        );

        Cache::forget("parametre.{$cle}");

        return $param;
    }

    /**
     * Tous les paramètres d'un groupe.
     */
    public static function groupe(string $groupe): Collection
    {
        return static::where('groupe', $groupe)->orderBy('cle')->get();
    }
}
