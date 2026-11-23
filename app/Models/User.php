<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Filament\Models\Contracts\FilamentUser;


#[Fillable(['name', 'email', 'password', 'sede_id', 'activo'])]
#[Hidden(['password', 'remember_token'])]

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use LogsActivity;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('administrador');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            // Solo registra los campos que cambian, sin ensuciar la base de datos
            ->logOnlyDirty()
            // No falla si no hay cambios
            ->dontSubmitEmptyLogs()
            // Opcional: puedes especificar qué campos registrar exactamente
            ->logFillable();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->activo === true; // O la lógica que prefieras
    }
}
