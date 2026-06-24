<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SchoolProfile extends Model
{
    protected $table = 'school_profile';

    protected $fillable = [
        'name','short_name','address','logo','stamp','motto',
        'phone','email','website','principal_name',
        'waec_centre_number','neco_centre_number','rc_number',
        'state','lga','city','ca_weight','exam_weight',
        'currency_symbol','timezone',
        'paystack_public_key','paystack_secret_key',
        'termii_api_key','termii_sender_id',
        'mail_from_address','mail_from_name',
        'sms_on_absence','sms_on_payment','sms_on_result_publish','email_on_absence',
    ];

    protected $casts = [
        'sms_on_absence'        => 'boolean',
        'sms_on_payment'        => 'boolean',
        'sms_on_result_publish' => 'boolean',
        'ca_weight'             => 'integer',
        'exam_weight'           => 'integer',
    ];

    public static function current(): static
    {
        return Cache::remember('school_profile', 3600, fn () => static::firstOrFail());
    }

    public static function clearCache(): void
    {
        Cache::forget('school_profile');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
    }
}
