<?php

namespace App\Model\Noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $deal_no
 * @property string $sales_office_no
 * @property string $memo_doc_no
 * @property string $prod_no_1
 * @property string $prod_no_2
 * @property string $deal_desc
 * @property int $promo_no
 * @property int $sub_no
 * @property integer $deal_qty1
 * @property int $deal_qty2
 * @property float $deal_amt
 * @property string $deal_fromdate
 * @property string $deal_todate
 * @property integer $lvl_1
 * @property integer $lvl_2
 * @property integer $lvl_3
 * @property string $lvl_4
 * @property string $lvl_5
 * @property integer $lvl_6
 * @property boolean $containers_granted
 * @property boolean $deleted
 * @property string $post_date
 * @property string $extract_date
 * @property string $sls_actng_code
 * @property string $pser_cd
 * @property int $c_type
 * @property string $sls_actng_code1
 * @property int $suppress
 * @property string $outlet_code
 * @property string $added_by
 * @property string $added_when
 * @property string $edited_by
 * @property string $edited_when
 * @property string $deleted_by
 * @property string $deleted_when
 * @property boolean $centrix_synced
 * @property boolean $onesoas_synced
 * @property string $ms_dynamics_key
 * @property boolean $msd_synced
 */
class DealsPromotion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'deals_promotion';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'deal_no';

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
    protected $fillable = ['sales_office_no', 'memo_doc_no', 'prod_no_1', 'prod_no_2', 'deal_desc', 'promo_no', 'sub_no', 'deal_qty1', 'deal_qty2', 'deal_amt', 'deal_fromdate', 'deal_todate', 'lvl_1', 'lvl_2', 'lvl_3', 'lvl_4', 'lvl_5', 'lvl_6', 'containers_granted', 'deleted', 'post_date', 'extract_date', 'sls_actng_code', 'pser_cd', 'c_type', 'sls_actng_code1', 'suppress', 'outlet_code', 'added_by', 'added_when', 'edited_by', 'edited_when', 'deleted_by', 'deleted_when', 'centrix_synced', 'onesoas_synced', 'ms_dynamics_key', 'msd_synced'];
}
