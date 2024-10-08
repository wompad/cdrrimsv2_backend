<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SexAgeSectorData extends Model
{
    use HasFactory;

    protected $table = 'tbl_sex_age_sector_data';

    protected $primaryKey = 'uuid';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'province_psgc_code',
        'municipality_psgc_code',
        'disaster_report_uuid',
        'infant_male_cum', 'infant_male_now', 'infant_female_cum', 'infant_female_now',
        'toddlers_male_cum', 'toddlers_male_now', 'toddlers_female_cum', 'toddlers_female_now',
        'preschoolers_male_cum', 'preschoolers_male_now', 'preschoolers_female_cum', 'preschoolers_female_now',
        'school_age_male_cum', 'school_age_male_now', 'school_age_female_cum', 'school_age_female_now',
        'teenage_male_cum', 'teenage_male_now', 'teenage_female_cum', 'teenage_female_now',
        'adult_male_cum', 'adult_male_now', 'adult_female_cum', 'adult_female_now',
        'elderly_male_cum', 'elderly_male_now', 'elderly_female_cum', 'elderly_female_now',
        'pregnant_cum', 'pregnant_now', 'lactating_cum', 'lactating_now',
        'child_headed_male_cum', 'child_headed_male_now', 'child_headed_female_cum', 'child_headed_female_now',
        'single_headed_male_cum', 'single_headed_male_now', 'single_headed_female_cum', 'single_headed_female_now',
        'solo_parent_male_cum', 'solo_parent_male_now', 'solo_parent_female_cum', 'solo_parent_female_now',
        'pwd_male_cum', 'pwd_male_now', 'pwd_female_cum', 'pwd_female_now',
        'ip_male_cum', 'ip_male_now', 'ip_female_cum', 'ip_female_now',
        'fourps_male_cum', 'fourps_male_now', 'fourps_female_cum', 'fourps_female_now'
    ];
}
