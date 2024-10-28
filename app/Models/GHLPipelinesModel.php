<?php

namespace App\Models;

use CodeIgniter\Model;

class GHLPipelinesModel extends Model
{
    protected $table            = 'ghl_pipelines';
    protected $primaryKey       = 'ghlPipelineId';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'ghlPipelineId',
        'externalId',
        'externalLocationId',
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

    public function stages()
    {
        return $this->hasMany(GHLStagesModel::class, 'ghlPipelineId', 'ghlPipelineId');
    }

    public function getAll()
    {
        // Fetch all
        return $this->findAll();
    }

    public function getPipelinesWithStages()
    {
        // Define columns to select
        $this->select('ghl_pipelines.*, ghl_stages.name');

        // Define join condition
        $this->join('ghl_stages', 'ghl_stages.ghlPipelineId = ghl_pipelines.ghlPipelineId', 'left');

        // Fetch all
        return $this->findAll();
    }
}
