<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property integer $id
 * @property string $discount_m_case_no
 * @property string $disc_type_no
 * @property string $sales_office_no
 * @property int $location_id
 * @property string $document_no
 * @property string $discount_case_cd
 * @property string $description
 * @property string $product_no
 * @property int $min
 * @property int $max
 * @property float $amount
 * @property float $percentage
 * @property string $from_date
 * @property string $to_date
 * @property string $office_source
 * @property boolean $deleted
 * @property string $post_date
 * @property string $extract_date
 * @property string $sales_accounting_code
 * @property string $pser_cd
 * @property string $level
 * @property string $level_4
 * @property string $level_5
 * @property string $added_by
 * @property string $added_when
 * @property string $deleted_by
 * @property string $deleted_when
 * @property int $is_synced
 * @property string $updated_by
 * @property string $updated_when
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 * @property boolean $msd_synced
 */
class DiscountCase extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'discount_m_case';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'integer';

    /**
     * @var array
     */
    protected $fillable = ['discount_m_case_no', 'disc_type_no', 'sales_office_no', 'location_id', 'document_no', 'discount_case_cd', 'description', 'product_no', 'min', 'max', 'amount', 'percentage', 'from_date', 'to_date', 'office_source', 'deleted', 'post_date', 'extract_date', 'sales_accounting_code', 'pser_cd', 'level', 'level_4', 'level_5', 'added_by', 'added_when', 'deleted_by', 'deleted_when', 'is_synced', 'updated_by', 'updated_when', 'centrix_synced', 'onesoas_synced', 'msd_synced'];
}
