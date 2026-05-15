<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'core_employee';    
    protected $fillable = ['company_id', 'company_name', 'status', 'employee_id', 'emp_code', 'emp_name', 'emp_email', 'emp_department', 'emp_contact', 'emp_reporting'];

    /**
     * Get accessions that use this category
     */
}
