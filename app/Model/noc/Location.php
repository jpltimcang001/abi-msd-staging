<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property SalesOffice $salesOffice
 * @property Bank[] $banks
 * @property Caf[] $cafs
 * @property LocationDetail $locationDetail
 * @property PserLocation[] $pserLocations
 * @property TempSalesOrder[] $tempSalesOrders
 * @property int $id
 * @property string $sales_office_no
 * @property string $name
 * @property string $code
 * @property string $code2
 * @property int $store_type_id
 * @property string $distribution_chanel
 * @property string $tin
 * @property string $address1
 * @property string $address2
 * @property int $barangay_id
 * @property int $municipal_id
 * @property int $province_id
 * @property int $region_id
 * @property string $owner_name
 * @property string $longitude
 * @property string $latitude
 * @property string $added_by
 * @property string $added_when
 * @property string $updated_when
 * @property string $updated_by
 * @property string $deleted_when
 * @property string $deleted_by
 * @property boolean $deleted
 * @property boolean $status
 * @property string $owner_contact
 * @property string $longitude2
 * @property string $latitude2
 * @property string $sub_so_no
 * @property int $wholesaler_id
 * @property int $is_synced
 * @property string $picture
 * @property int $priority
 * @property boolean $location_type
 * @property string $room_floor_bldg
 * @property string $st_subd
 * @property string $barangay
 * @property string $town_no
 * @property string $region_no
 * @property string $country
 * @property string $zip_code
 * @property string $account_no
 * @property string $account_name
 * @property string $tel_no
 * @property string $fax_no
 * @property string $route_seq_code
 * @property string $salesman_code
 * @property string $sub_salesman_code
 * @property string $tax_classification
 * @property string $credit_term
 * @property int $limit_fulls
 * @property int $limit_mts
 * @property boolean $vat_tag
 * @property string $date_open
 * @property string $as_name_1
 * @property string $as_position_1
 * @property string $as_name_2
 * @property string $as_position_2
 * @property string $cp_name_1
 * @property string $cp_position_1
 * @property string $cp_contact_no_1
 * @property string $cp_name_2
 * @property string $cp_position_2
 * @property string $cp_contact_no_2
 * @property int $district_id
 * @property string $bank
 * @property string $branch
 * @property string $sss_no
 * @property string $email_address
 * @property string $service_model
 * @property string $service_call_days
 * @property string $location_branch
 * @property float $sv_potential_sales
 * @property float $sv_wv_beer
 * @property float $sv_wv_water
 * @property float $sv_wv_softdrinks
 * @property float $sv_wv_cobra
 * @property int $sv_outlets_status
 * @property string $vat_exempt_classification
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 * @property boolean $sys_21_synced
 */
class Location extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'location';
    const CREATED_AT = 'added_when';
    const UPDATED_AT = 'updated_when';

    /**
     * @var array
     */
    protected $fillable = ['sales_office_no', 'name', 'code', 'code2', 'store_type_id', 'distribution_chanel', 'tin', 'address1', 'address2', 'barangay_id', 'municipal_id', 'province_id', 'region_id', 'owner_name', 'longitude', 'latitude', 'added_by', 'added_when', 'updated_when', 'updated_by', 'deleted_when', 'deleted_by', 'deleted', 'status', 'owner_contact', 'longitude2', 'latitude2', 'sub_so_no', 'wholesaler_id', 'is_synced', 'picture', 'priority', 'location_type', 'room_floor_bldg', 'st_subd', 'barangay', 'town_no', 'region_no', 'country', 'zip_code', 'account_no', 'account_name', 'tel_no', 'fax_no', 'route_seq_code', 'salesman_code', 'sub_salesman_code', 'tax_classification', 'credit_term', 'limit_fulls', 'limit_mts', 'vat_tag', 'date_open', 'as_name_1', 'as_position_1', 'as_name_2', 'as_position_2', 'cp_name_1', 'cp_position_1', 'cp_contact_no_1', 'cp_name_2', 'cp_position_2', 'cp_contact_no_2', 'district_id', 'bank', 'branch', 'sss_no', 'email_address', 'service_model', 'service_call_days', 'location_branch', 'sv_potential_sales', 'sv_wv_beer', 'sv_wv_water', 'sv_wv_softdrinks', 'sv_wv_cobra', 'sv_outlets_status', 'vat_exempt_classification', 'centrix_synced', 'onesoas_synced', 'sys_21_synced'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'mysql';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salesOffice()
    {
        return $this->belongsTo('App\Model\noc\SalesOffice', 'sales_office_no', 'no');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function banks()
    {
        return $this->hasMany('App\Model\noc\Bank');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafs()
    {
        return $this->hasMany('App\Model\noc\Caf');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function locationDetail()
    {
        return $this->hasOne('App\Model\noc\LocationDetail', 'location_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pserLocations()
    {
        return $this->hasMany('App\Model\noc\PserLocation');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function tempSalesOrders()
    {
        return $this->hasMany('App\Model\noc\TempSalesOrder');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function barangay()
    {
        return $this->belongsTo('App\Model\noc\Barangay', 'barangay_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function district()
    {
        return $this->belongsTo('App\Model\noc\District', 'district_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function province()
    {
        return $this->belongsTo('App\Model\noc\Province', 'province_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region()
    {
        return $this->belongsTo('App\Model\noc\Region', 'region_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function municipal()
    {
        return $this->belongsTo('App\Model\noc\Municipal', 'municipal_id');
    }


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storeType()
    {
        return $this->belongsTo('App\Model\noc\StoreType', 'store_type_id');
    }
}
