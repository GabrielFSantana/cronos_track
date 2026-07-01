<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ActivityModel;
use CodeIgniter\HTTP\ResponseInterface;

class ManualController extends BaseController
{
    /**
     * Cria um único lançamento manual de tempo. "origem" é sempre
     * forçado para "manual" — o cliente não escolhe esse valor.
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (! is_array($payload)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Corpo da requisição inválido.',
            ]);
        }

        $row = [
            'origem'         => 'manual',
            'app_ou_dominio' => $payload['app_ou_dominio'] ?? null,
            'titulo'         => $payload['titulo'] ?? null,
            'projeto_id'     => $payload['projeto_id'] ?? null,
            'inicio'         => $payload['inicio'] ?? null,
            'fim'            => $payload['fim'] ?? null,
        ];

        if (! empty($row['inicio']) && ! empty($row['fim']) && strtotime($row['fim']) <= strtotime($row['inicio'])) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Dados inválidos.',
                'errors'  => ['fim' => '"fim" deve ser posterior a "inicio".'],
            ]);
        }

        $model = new ActivityModel();

        try {
            $id = $model->insert($row, true);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Falha ao salvar o lançamento.',
            ]);
        }

        if ($id === false) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Dados inválidos.',
                'errors'  => $model->errors(),
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status' => 'success',
            'id'     => $id,
        ]);
    }
}
