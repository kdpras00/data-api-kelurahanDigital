<?php
namespace App\Models;

use App\Traits\UUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobVacancy extends Model
{
    //
    use UUID, SoftDeletes, HasFactory;

    protected $fillable = [
        'thumbnail',
        'job_title',
        'description',
        'company_in_charge',
        'start_date',
        'end_date',
        'salary',
        'job_status',
    ];

    protected $casts = [
        'salary' => 'decimal:2',
    ];

    public function scopeSearch($query, $search)
    {
        return $query->where('job_title', 'like', '%' . $search . '%')
            ->orWhere('company_in_charge', 'like', '%' . $search . '%');
    }

    public function jobApplicants()
    {
        return $this->hasMany(JobApplicant::class);
    }

}
