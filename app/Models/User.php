<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role as spatieRole;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, InteractsWithMedia, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'no_ic',
        'jenis_pengguna_id',
        'no_telefon',
        'jawatan_id',
        'jabatan_id',
        'agensi_id',
        'gred_jawatan_id',
        'kementerian_id',
        'pajabat_id',
        'bahagian_id',
        'negeri_id',
        'daerah_id',
        'status_pengguna_id',
        'catatan',
        'is_super_admin',
        'row_status',
        'dibuat_oleh',
        'dibuat_pada',
        'dikemaskini_oleh',
        'dikemaskini_pada',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function agensi()
    {
        return $this->belongsTo(\App\Models\refAgensi::class, 'agensi_id', 'id')->withDefault();
    }

    public function bahagian()
    {        
        return $this->belongsTo(\App\Models\refBahagian::class, 'bahagian_id', 'id')->withDefault();
    }

    public function daerah()
    {        
        return $this->belongsTo(\App\Models\refDaerah::class, 'daerah_id', 'id')->withDefault();
    }

    public function gredJawatan()
    {        
        return $this->belongsTo(\App\Models\refGredJawatan::class, 'gred_jawatan_id', 'id')->withDefault();
    }

    public function jabatan()
    {        
        return $this->belongsTo(\App\Models\refJabatan::class, 'jabatan_id', 'id')->withDefault();
    }

    public function jawatan()
    {
        return $this->belongsTo(\App\Models\refJawatan::class, 'jawatan_id', 'id')->withDefault();        
    }

    public function kementerian()
    {
        return $this->belongsTo(\App\Models\refKementerian::class, 'kementerian_id', 'id')->withDefault();        
    }

    public function jenisPengguna()
    {        
        return $this->belongsTo(\App\Models\refJenisPengguna::class, 'jenis_pengguna_id', 'id')->withDefault();
    }

    public function negeri()
    {        
        return $this->belongsTo(\App\Models\refNegeri::class, 'negeri_id', 'id')->withDefault();
    }

    public function jawatanName()
    {
        return $this->belongsTo(\App\Models\refJawatan::class, 'jawatan_id', 'kod_jawatan')->withDefault();
    }
    public function updatedBy()
    {        
        return $this->belongsTo(\App\Models\User::class, 'dikemaskini_oleh', 'id')->withDefault();
    }

    public function isJps()
    {
        return $this->jenisPengguna->nama_jenis_pengguna == 'Pengguna JPS';
    }


    public function profileImageUrl()
    {
        if($this->hasMedia('profile_image')) {
            return $this->getFirstMediaUrl('profile_image');
        }else {
            return '';
        }
    }

    public function addRolePermissionsToUser(spatieRole $role)
    {
        $permissions = $role->permissions;
        foreach ($permissions as $permission) {
            $this->givePermissionTo($permission);
        }
    }

    public function userTypeRole()
    {
        return $this->hasMany(UserTypeRole::class, 'user_type_id', 'user_type_id');
    }

}
