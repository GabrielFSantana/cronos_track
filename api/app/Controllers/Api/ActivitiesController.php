<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ActivityModel;
use CodeIgniter\HTTP\ResponseInterface;

class ActivitiesController extends BaseController
{
    /**
     * Recebe um lote de sessões (app, navegador ou manual) e grava todas
     * de uma vez. Se qualquer item do lote for inválido, nada é salvo.
     */
    public function create(): ResponseInterface
    {
        $payload = $this->request->getJSON(true);

        if (! is_array($payload) || ! is_array($payload['activities'] ?? null) || $payload['activities'] === []) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Envie um array "activities" com ao menos um item.',
            ]);
        }

        $model  = new ActivityModel();
        $rows   = [];
        $errors = [];

        foreach ($payload['activities'] as $index => $item) {
            if (! is_array($item)) {
                $errors[$index] = ['_' => 'Item inválido.'];
                continue;
            }

            $row = [
                'origem'         => $item['origem'] ?? null,
                'app_ou_dominio' => $item['app_ou_dominio'] ?? null,
                'titulo'         => $item['titulo'] ?? null,
                'projeto_id'     => $item['projeto_id'] ?? null,
                'inicio'         => $item['inicio'] ?? null,
                'fim'            => $item['fim'] ?? null,
            ];

            if (! $model->validate($row)) {
                $errors[$index] = $model->errors();
                continue;
            }

            if (strtotime($row['fim']) <= strtotime($row['inicio'])) {
                $errors[$index] = ['fim' => '"fim" deve ser posterior a "inicio".'];
                continue;
            }

            $rows[] = $row;
        }

        if ($errors !== []) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => 'Um ou mais itens são inválidos. Nenhum registro foi salvo.',
                'errors'  => $errors,
            ]);
        }

        $db = db_connect();

        try {
            $db->transStart();
            $model->insertBatch($rows);
            $db->transComplete();
        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Falha ao salvar as atividades.',
            ]);
        }

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'message' => 'Falha ao salvar as atividades.',
            ]);
        }

        return $this->response->setStatusCode(201)->setJSON([
            'status'   => 'success',
            'inserted' => count($rows),
        ]);
    }
}
