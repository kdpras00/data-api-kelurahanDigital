<?php
namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplicant extends Model
{
    //
    use UUID, SoftDeletes, HasFactory;

    protected $fillable = [
        'job_vacancy_id',
        'user_id',
        'status',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->whereHas('user', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })->orWhereHas('jobVacancy', function ($query) use ($search) {
            $query->where('job_title', 'like', '%' . $search . '%');
            $query->orWhere('company_in_charge', 'like', '%' . $search . '%');
        })->orWhere('status', 'like', '%' . $search . '%');
    }

    public function jobVacancy()
    {
        return $this->belongsTo(JobVacancy::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
