<?php

namespace App\Model\wms;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Aisle[] $aisles
 * @property AuthitemDatum[] $authitemDatas
 * @property BatchUpload[] $batchUploads
 * @property BatchUploadDetail[] $batchUploadDetails
 * @property Brand[] $brands
 * @property BrandCategory[] $brandCategories
 * @property DefaultPassword[] $defaultPasswords
 * @property Email[] $emails
 * @property Employee[] $employees
 * @property EmployeePoi[] $employeePois
 * @property EmployeeStatus[] $employeeStatuses
 * @property EmployeeType[] $employeeTypes
 * @property Image[] $images
 * @property ImagesNaiProject[] $imagesNaiProjects
 * @property IntegrationSync[] $integrationSyncs
 * @property IntegrationSyncDetail[] $integrationSyncDetails
 * @property Inventory[] $inventories
 * @property InventoryHistory[] $inventoryHistories
 * @property InventorySummary[] $inventorySummaries
 * @property Level[] $levels
 * @property Notification[] $notifications
 * @property Offtake[] $offtakes
 * @property OfftakeAvgDaily[] $offtakeAvgDailies
 * @property OfftakeDetail[] $offtakeDetails
 * @property OnShelfAvailability[] $onShelfAvailabilities
 * @property PhysicalCount[] $physicalCounts
 * @property PhysicalCountDetail[] $physicalCountDetails
 * @property PoiCategory[] $poiCategories
 * @property PoiCustomDatum[] $poiCustomDatas
 * @property PoiSku[] $poiSkus
 * @property PoiSubCategory[] $poiSubCategories
 * @property SalesGroup[] $salesGroups
 * @property SalesOffice[] $salesOffices
 * @property Shelf[] $shelves
 * @property Sku[] $skus
 * @property SkuCategory[] $skuCategories
 * @property SkuClassification[] $skuClassifications
 * @property SkuCombine[] $skuCombines
 * @property SkuConvertion[] $skuConvertions
 * @property SkuCustomDatum[] $skuCustomDatas
 * @property SkuGroup[] $skuGroups
 * @property SkuImage[] $skuImages
 * @property SkuLocationRestock[] $skuLocationRestocks
 * @property SkuSalesOffice[] $skuSalesOffices
 * @property SkuStatus[] $skuStatuses
 * @property SkuSubCategory[] $skuSubCategories
 * @property Supplier[] $suppliers
 * @property SupplierCategory[] $supplierCategories
 * @property Uom[] $uoms
 * @property User[] $users
 * @property UserAvatar[] $userAvatars
 * @property Zone[] $zones
 * @property string $company_id
 * @property int $status_id
 * @property string $industry
 * @property string $code
 * @property string $name
 * @property string $address1
 * @property string $address2
 * @property string $city
 * @property int $province
 * @property string $country
 * @property string $phone
 * @property string $fax
 * @property string $zip_code
 * @property string $created_date
 * @property string $created_by
 * @property string $updated_date
 * @property string $updated_by
 * @property string $url
 */
class Company extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'company';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'company_id';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['status_id', 'industry', 'code', 'name', 'address1', 'address2', 'city', 'province', 'country', 'phone', 'fax', 'zip_code', 'created_date', 'created_by', 'updated_date', 'updated_by', 'url'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'noc';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function aisles()
    {
        return $this->hasMany('App\Model\wms\Aisle', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function authitemDatas()
    {
        return $this->hasMany('App\Model\wms\AuthitemDatum', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batchUploads()
    {
        return $this->hasMany('App\Model\wms\BatchUpload', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function batchUploadDetails()
    {
        return $this->hasMany('App\Model\wms\BatchUploadDetail', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function brands()
    {
        return $this->hasMany('App\Model\wms\Brand', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function brandCategories()
    {
        return $this->hasMany('App\Model\wms\BrandCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function defaultPasswords()
    {
        return $this->hasMany('App\Model\wms\DefaultPassword', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function emails()
    {
        return $this->hasMany('App\Model\wms\Email', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employees()
    {
        return $this->hasMany('App\Model\wms\Employee', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeePois()
    {
        return $this->hasMany('App\Model\wms\EmployeePoi', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeStatuses()
    {
        return $this->hasMany('App\Model\wms\EmployeeStatus', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function employeeTypes()
    {
        return $this->hasMany('App\Model\wms\EmployeeType', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function images()
    {
        return $this->hasMany('App\Model\wms\Image', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function imagesNaiProjects()
    {
        return $this->hasMany('App\Model\wms\ImagesNaiProject', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function integrationSyncs()
    {
        return $this->hasMany('App\Model\wms\IntegrationSync', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function integrationSyncDetails()
    {
        return $this->hasMany('App\Model\wms\IntegrationSyncDetail', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventories()
    {
        return $this->hasMany('App\Model\wms\Inventory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventoryHistories()
    {
        return $this->hasMany('App\Model\wms\InventoryHistory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventorySummaries()
    {
        return $this->hasMany('App\Model\wms\InventorySummary', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function levels()
    {
        return $this->hasMany('App\Model\wms\Level', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notifications()
    {
        return $this->hasMany('App\Model\wms\Notification', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakes()
    {
        return $this->hasMany('App\Model\wms\Offtake', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakeAvgDailies()
    {
        return $this->hasMany('App\Model\wms\OfftakeAvgDaily', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function offtakeDetails()
    {
        return $this->hasMany('App\Model\wms\OfftakeDetail', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function onShelfAvailabilities()
    {
        return $this->hasMany('App\Model\wms\OnShelfAvailability', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physicalCounts()
    {
        return $this->hasMany('App\Model\wms\PhysicalCount', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function physicalCountDetails()
    {
        return $this->hasMany('App\Model\wms\PhysicalCountDetail', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poiCategories()
    {
        return $this->hasMany('App\Model\wms\PoiCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poiCustomDatas()
    {
        return $this->hasMany('App\Model\wms\PoiCustomDatum', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poiSkus()
    {
        return $this->hasMany('App\Model\wms\PoiSku', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poiSubCategories()
    {
        return $this->hasMany('App\Model\wms\PoiSubCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesGroups()
    {
        return $this->hasMany('App\Model\wms\SalesGroup', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salesOffices()
    {
        return $this->hasMany('App\Model\wms\SalesOffice', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function shelves()
    {
        return $this->hasMany('App\Model\wms\Shelf', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skus()
    {
        return $this->hasMany('App\Model\wms\Sku', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuCategories()
    {
        return $this->hasMany('App\Model\wms\SkuCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuClassifications()
    {
        return $this->hasMany('App\Model\wms\SkuClassification', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuCombines()
    {
        return $this->hasMany('App\Model\wms\SkuCombine', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuConvertions()
    {
        return $this->hasMany('App\Model\wms\SkuConvertion', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuCustomDatas()
    {
        return $this->hasMany('App\Model\wms\SkuCustomDatum', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuGroups()
    {
        return $this->hasMany('App\Model\wms\SkuGroup', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuImages()
    {
        return $this->hasMany('App\Model\wms\SkuImage', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuLocationRestocks()
    {
        return $this->hasMany('App\Model\wms\SkuLocationRestock', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuSalesOffices()
    {
        return $this->hasMany('App\Model\wms\SkuSalesOffice', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuStatuses()
    {
        return $this->hasMany('App\Model\wms\SkuStatus', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function skuSubCategories()
    {
        return $this->hasMany('App\Model\wms\SkuSubCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function suppliers()
    {
        return $this->hasMany('App\Model\wms\Supplier', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function supplierCategories()
    {
        return $this->hasMany('App\Model\wms\SupplierCategory', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function uoms()
    {
        return $this->hasMany('App\Model\wms\Uom', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany('App\Model\wms\User', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userAvatars()
    {
        return $this->hasMany('App\Model\wms\UserAvatar', null, 'company_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function zones()
    {
        return $this->hasMany('App\Model\wms\Zone', null, 'company_id');
    }
}
