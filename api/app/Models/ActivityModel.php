<?php

namespace App\Models;

use CodeIgniter\Model;

class ActivityModel extends Model
{
    protected $table            = 'activities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    // duracao_seg é coluna gerada (TIMESTAMPDIFF) e criado_em tem DEFAULT no banco:
    // nenhum dos dois deve ser preenchido pela aplicação.
    protected $allowedFields = [
        'origem',
        'app_ou_dominio',
        'titulo',
        'projeto_id',
        'inicio',
        'fim',
    ];

    protected $validationRules = [
        'origem'         => 'required|in_list[app,navegador,manual]',
        'app_ou_dominio' => 'required|max_length[255]',
        'titulo'         => 'permit_empty|max_length[500]',
        'projeto_id'     => 'permit_empty|is_natural_no_zero',
        'inicio'         => 'required|valid_date',
        'fim'            => 'required|valid_date',
    ];
}
