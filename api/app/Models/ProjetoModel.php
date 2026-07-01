<?php

namespace App\Models;

use CodeIgniter\Model;

class ProjetoModel extends Model
{
    protected $table            = 'projetos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useTimestamps    = false;

    protected $allowedFields = ['nome', 'cor'];

    protected $validationRules = [
        'nome' => 'required|max_length[100]',
        'cor'  => 'permit_empty|regex_match[/^#[0-9a-fA-F]{6}$/]',
    ];
}
