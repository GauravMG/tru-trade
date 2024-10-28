<?php

namespace App\Models;

use CodeIgniter\Model;

class GHLStagesModel extends Model
{
    protected $table            = 'ghl_stages';
    protected $primaryKey       = 'ghlStageId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ghlStageId',
        'ghlPipelineId',
        'externalId',
        'name',
        'createdAt',
        'updatedAt',
        'deletedAt'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'createdAt';
    protected $updatedField  = 'updatedAt';
    protected $deletedField  = 'deletedAt';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    protected function beforeInsert(array $data)
    {
        $data['data']['createdAt'] = date('Y-m-d H:i:s');
        $data['data']['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function beforeUpdate(array $data)
    {
        $data['data']['updatedAt'] = date('Y-m-d H:i:s');
        return $data;
    }

    public function getAll()
    {
        // Fetch all
        return $this->findAll();
    }
}
