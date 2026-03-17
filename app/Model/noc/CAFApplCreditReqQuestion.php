<?php

namespace App\Model\noc;

use Illuminate\Database\Eloquent\Model;

/**
 * @property CafApplCreditReqAnswer[] $cafApplCreditReqAnswers
 * @property int $id
 * @property string $added_by
 * @property string $added_when
 * @property boolean $deleted
 * @property string $deleted_by
 * @property string $deleted_when
 * @property string $question
 * @property string $title
 * @property string $updated_by
 * @property string $updated_when
 */
class CAFApplCreditReqQuestion extends Model
{
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'caf_appl_credit_req_question';

    /**
     * @var array
     */
    protected $fillable = ['added_by', 'added_when', 'deleted', 'deleted_by', 'deleted_when', 'question', 'title', 'updated_by', 'updated_when'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cafApplCreditReqAnswers()
    {
        return $this->hasMany('App\Model\noc\CafApplCreditReqAnswer', 'question_id');
    }
}
