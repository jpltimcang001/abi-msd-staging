<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property Caf $caf
 * @property CafApplCreditReqQuestion $cafApplCreditReqQuestion
 * @property CafApplCreditReqAttachment[] $cafApplCreditReqAttachments
 * @property int $id
 * @property int $caf_id
 * @property int $question_id
 * @property string $added_by
 * @property string $added_when
 * @property string $answer
 * @property boolean $centrix_synced
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property boolean $msd_synced
 * @property boolean $sys_21_synced
 * @property string $updated_by
 * @property string $updated_when
 */
class CAFApplCreditReqAnswer extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'caf_appl_credit_req_answer';

    /**
     * @var array
     */
    protected $fillable = ['caf_id', 'question_id', 'added_by', 'added_when', 'answer', 'centrix_synced', 'deleted', 'deleted_by', 'deleted_when', 'msd_synced', 'sys_21_synced', 'updated_by', 'updated_when'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function caf()
    {
        return $this->belongsTo('App\Model\noc\Caf');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cafApplCreditReqQuestion()
    {
        return $this->belongsTo('App\Model\noc\CafApplCreditReqQuestion', 'question_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafApplCreditReqAttachments()
    {
        return $this->hasMany('App\Model\noc\CafApplCreditReqAttachment');
    }
}
