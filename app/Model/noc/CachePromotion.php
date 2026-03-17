<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $no
 * @property string $name
 * @property string $description
 * @property string $sales_office_no
 * @property string $short_desc
 * @property string $start_date
 * @property string $end_date
 * @property string $scheme_type
 * @property string $scheme_activate
 * @property string $exclusive_promo
 * @property string $discount
 * @property string $foc
 * @property string $bundle_validation
 * @property string $link_bundle
 * @property string $foc_scheme
 * @property string $discount_scheme
 * @property string $request_no
 * @property string $added_by
 * @property string $updated_by
 */
class CachePromotion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'cache_promotion';

    /**
     * @var array
     */
    protected $fillable = ['no', 'name', 'description', 'sales_office_no', 'short_desc', 'start_date', 'end_date', 'scheme_type', 'scheme_activate', 'exclusive_promo', 'discount', 'foc', 'bundle_validation', 'link_bundle', 'foc_scheme', 'discount_scheme', 'request_no', 'added_by', 'updated_by'];
}
